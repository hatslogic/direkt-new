<?php
declare(strict_types=1);

namespace Swag\Security\Fixes\NEXT40474;

use Shopware\Core\Checkout\Customer\CustomerCollection;
use Shopware\Core\Checkout\Customer\Service\EmailIdnConverter;
use Shopware\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientCollection;
use Shopware\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientEntity;
use Shopware\Core\Content\Newsletter\Event\NewsletterConfirmEvent;
use Shopware\Core\Content\Newsletter\Event\NewsletterRegisterEvent;
use Shopware\Core\Content\Newsletter\Event\NewsletterSubscribeUrlEvent;
use Shopware\Core\Content\Newsletter\NewsletterException;
use Shopware\Core\Content\Newsletter\SalesChannel\AbstractNewsletterSubscribeRoute;
use Shopware\Core\Content\Newsletter\SalesChannel\NewsletterSubscribeRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\RateLimiter\RateLimiter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\Validation\BuildValidationEvent;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\System\SalesChannel\Aggregate\SalesChannelDomain\SalesChannelDomainEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SalesChannel\StoreApiCustomFieldMapper;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Shopware\Core\System\SalesChannel\NoContentResponse;
use Shopware\Core\Framework\RateLimiter\Exception\RateLimitExceededException;
use Shopware\Core\Content\Newsletter\Aggregate\NewsletterRecipient\NewsletterRecipientDefinition;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @phpstan-type SubscribeRequest array{email: string, storefrontUrl: string, option: string, firstName?: string, lastName?: string, zipCode?: string, city?: string, street?: string, salutationId?: string}
 */
class FixNewsletterSubscribeRoute extends AbstractNewsletterSubscribeRoute
{
    /**
     * @param EntityRepository<CustomerCollection> $customerRepository
     * @param EntityRepository<NewsletterRecipientCollection> $newsletterRecipientRepository
     */
    public function __construct(
        private readonly AbstractNewsletterSubscribeRoute $decorated,
        private readonly EntityRepository $customerRepository,
        private readonly EntityRepository $newsletterRecipientRepository,
        private readonly SystemConfigService $systemConfigService,
        private readonly DataValidator $validator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RateLimiter $rateLimiter,
        private readonly RequestStack $requestStack,
        private readonly StoreApiCustomFieldMapper $customFieldMapper
    )
    {
    }

    public function getDecorated(): AbstractNewsletterSubscribeRoute
    {
        return $this->decorated;
    }

    public function subscribe(RequestDataBag $dataBag, SalesChannelContext $context, bool $validateStorefrontUrl = true): NoContentResponse
    {
        $doubleOptInDomain = $this->systemConfigService->getString(
            'core.newsletter.doubleOptInDomain',
            $context->getSalesChannelId()
        );
        if ($doubleOptInDomain !== '') {
            $dataBag->set('storefrontUrl', $doubleOptInDomain);
            $validateStorefrontUrl = false;
        }

        EmailIdnConverter::encodeDataBag($dataBag);

        $validator = $this->getOptInValidator($dataBag, $context, $validateStorefrontUrl);

        $this->validator->validate($dataBag->all(), $validator);

        if (($request = $this->requestStack->getMainRequest()) !== null && $request->getClientIp() !== null) {
            try {
                $this->rateLimiter->ensureAccepted(RateLimiter::NEWSLETTER_FORM, $request->getClientIp());
            } catch (RateLimitExceededException $e) {
                throw NewsletterException::newsletterThrottled($e->getWaitTime());
            }
        }

        /** @var SubscribeRequest $data */
        $data = $dataBag->only(
            'email',
            'title',
            'firstName',
            'lastName',
            'zipCode',
            'city',
            'street',
            'salutationId',
            'option',
            'storefrontUrl',
            'customFields'
        );

        $recipientId = $this->getNewsletterRecipientId($data['email'], $context);

        if (isset($recipientId)) {
            /** @var NewsletterRecipientEntity $recipient */
            $recipient = $this->newsletterRecipientRepository->search(new Criteria([$recipientId]), $context->getContext())->first();

            // If the user was previously subscribed but has unsubscribed now, the `getConfirmedAt()`
            // will still be set. So we need to check for the status as well.
            if ($recipient->getStatus() !== NewsletterSubscribeRoute::STATUS_OPT_OUT && $recipient->getConfirmedAt()) {
                return new NoContentResponse();
            }
        }

        $data = $this->completeData($data, $context);
        if ($dataBag->get('customFields') instanceof RequestDataBag) {
            $data['customFields'] = $this->customFieldMapper->map(
                NewsletterRecipientDefinition::ENTITY_NAME,
                $dataBag->get('customFields')
            );
        }

        $this->newsletterRecipientRepository->upsert([$data], $context->getContext());

        $recipient = $this->getNewsletterRecipient('email', $data['email'], $context);
        $recipientEmail = $recipient->getEmail();

        if (!$this->isNewsletterDoi($context, $recipientEmail)) {
            $event = new NewsletterConfirmEvent($context->getContext(), $recipient, $context->getSalesChannelId());
            $this->eventDispatcher->dispatch($event);

            return new NoContentResponse();
        }

        $hashedEmail = hash('sha1', $data['email']);
        $url = $this->getSubscribeUrl($context, $hashedEmail, $data['hash'], $data, $recipient);

        $event = new NewsletterRegisterEvent($context->getContext(), $recipient, $url, $context->getSalesChannelId());
        $this->eventDispatcher->dispatch($event);

        return new NoContentResponse();
    }

    /**
     * Determines if double opt-in (DOI) is required for newsletter subscription.
     *
     * For guest users: use general DOI setting
     * For logged-in users:
     * If DOI for registered customers is enabled: always require DOI
     * If DOI for registered customers is disabled and general DOI is disabled: never require DOI
     * If DOI for registered customers is disabled and general DOI is enabled: require DOI if the recipient email is different from the customer's email
     */
    private function isNewsletterDoi(SalesChannelContext $context, ?string $recipientEmail): bool
    {
        $salesChannelId = $context->getSalesChannelId();
        $customerId = $context->getCustomerId();
        $isDoubleOptIn = $this->systemConfigService->getBool('core.newsletter.doubleOptIn', $salesChannelId);
        $isDoubleOptInRegistered = $this->systemConfigService->getBool('core.newsletter.doubleOptInRegistered', $salesChannelId);

        if ($customerId === null) {
            return $isDoubleOptIn;
        }

        if ($isDoubleOptInRegistered) {
            return true;
        }

        if (!$isDoubleOptIn) {
            return false;
        }

        $customerEmail = $this->getCustomerEmail($context, $customerId);

        return $customerEmail !== $recipientEmail;
    }

    private function getCustomerEmail(SalesChannelContext $context, string $customerId): ?string
    {
        $criteria = new Criteria([$customerId]);

        $customer = $this->customerRepository->search($criteria, $context->getContext())->getEntities()->first();

        return $customer?->getEmail();
    }

    private function getOptInValidator(DataBag $dataBag, SalesChannelContext $context, bool $validateStorefrontUrl): DataValidationDefinition
    {
        $definition = new DataValidationDefinition('newsletter_recipient.create');
        $definition->add('email', new NotBlank(), new Email())
            ->add('option', new NotBlank(), new Choice(array_keys($this->getOptionSelection($context, $dataBag->get('email')))));

        if (!empty($dataBag->get('firstName'))) {
            $definition->add('firstName', new NotBlank(), new Regex([
                'pattern' => NewsletterSubscribeRoute::DOMAIN_NAME_REGEX,
                'match' => false,
            ]));
        }

        if (!empty($dataBag->get('lastName'))) {
            $definition->add('lastName', new NotBlank(), new Regex([
                'pattern' => NewsletterSubscribeRoute::DOMAIN_NAME_REGEX,
                'match' => false,
            ]));
        }

        if ($validateStorefrontUrl) {
            $definition
                ->add('storefrontUrl', new NotBlank(), new Choice(array_values($this->getDomainUrls($context))));
        }

        $validationEvent = new BuildValidationEvent($definition, $dataBag, $context->getContext());
        $this->eventDispatcher->dispatch($validationEvent, $validationEvent->getName());

        return $definition;
    }

    /**
     * @param SubscribeRequest $data
     *
     * @return array{id: string, languageId: string, salesChannelId: string, status: string, hash: string, email: string, storefrontUrl: string, firstName?: string, lastName?: string, zipCode?: string, city?: string, street?: string, salutationId?: string}
     */
    private function completeData(array $data, SalesChannelContext $context): array
    {
        $id = $this->getNewsletterRecipientId($data['email'], $context);

        $data['id'] = $id ?: Uuid::randomHex();
        $data['languageId'] = $context->getLanguageId();
        $data['salesChannelId'] = $context->getSalesChannelId();
        $data['status'] = $this->getOptionSelection($context, $data['email'])[$data['option']];
        $data['hash'] = Uuid::randomHex();

        return $data;
    }

    private function getNewsletterRecipientId(string $email, SalesChannelContext $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new MultiFilter(MultiFilter::CONNECTION_AND, [
                new EqualsFilter('email', $email),
                new EqualsFilter('salesChannelId', $context->getSalesChannelId()),
            ]),
        );
        $criteria->setLimit(1);

        return $this->newsletterRecipientRepository
            ->searchIds($criteria, $context->getContext())
            ->firstId();
    }

    /**
     * @return array<string, string>
     */
    private function getOptionSelection(SalesChannelContext $context, ?string $recipientEmail): array
    {
        return [
            NewsletterSubscribeRoute::OPTION_DIRECT => $this->isNewsletterDoi($context, $recipientEmail) ? NewsletterSubscribeRoute::STATUS_NOT_SET : NewsletterSubscribeRoute::STATUS_DIRECT,
            NewsletterSubscribeRoute::OPTION_SUBSCRIBE => $this->isNewsletterDoi($context, $recipientEmail) ? NewsletterSubscribeRoute::STATUS_NOT_SET : NewsletterSubscribeRoute::STATUS_DIRECT,
            NewsletterSubscribeRoute::OPTION_CONFIRM_SUBSCRIBE => NewsletterSubscribeRoute::STATUS_OPT_IN,
            NewsletterSubscribeRoute::OPTION_UNSUBSCRIBE => NewsletterSubscribeRoute::STATUS_OPT_OUT,
        ];
    }

    private function getNewsletterRecipient(string $identifier, string $value, SalesChannelContext $context): NewsletterRecipientEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter($identifier, $value));
        $criteria->addFilter(new EqualsFilter('salesChannelId', $context->getSalesChannelId()));
        $criteria->addAssociation('salutation');
        $criteria->setLimit(1);

        /** @var NewsletterRecipientEntity|null $newsletterRecipient */
        $newsletterRecipient = $this->newsletterRecipientRepository->search($criteria, $context->getContext())->getEntities()->first();

        if (!$newsletterRecipient) {
            throw NewsletterException::recipientNotFound($identifier, $value);
        }

        return $newsletterRecipient;
    }

    /**
     * @return string[]
     */
    private function getDomainUrls(SalesChannelContext $context): array
    {
        $salesChannelDomainCollection = $context->getSalesChannel()->getDomains();
        if ($salesChannelDomainCollection === null) {
            return [];
        }

        return array_map(static fn (SalesChannelDomainEntity $domainEntity) => rtrim($domainEntity->getUrl(), '/'), $salesChannelDomainCollection->getElements());
    }

    /**
     * @param array{storefrontUrl: string} $data
     */
    private function getSubscribeUrl(
        SalesChannelContext $context,
        string $hashedEmail,
        string $hash,
        array $data,
        NewsletterRecipientEntity $recipient
    ): string {
        $urlTemplate = $this->systemConfigService->get(
            'core.newsletter.subscribeUrl',
            $context->getSalesChannelId()
        );
        if (!\is_string($urlTemplate)) {
            $urlTemplate = '/newsletter-subscribe?em=%%HASHEDEMAIL%%&hash=%%SUBSCRIBEHASH%%';
        }

        $urlEvent = new NewsletterSubscribeUrlEvent($context, $urlTemplate, $hashedEmail, $hash, $data, $recipient);
        $this->eventDispatcher->dispatch($urlEvent);

        return $data['storefrontUrl'] . str_replace(
                [
                    '%%HASHEDEMAIL%%',
                    '%%SUBSCRIBEHASH%%',
                ],
                [
                    $hashedEmail,
                    $hash,
                ],
                $urlEvent->getSubscribeUrl()
            );
    }
}
