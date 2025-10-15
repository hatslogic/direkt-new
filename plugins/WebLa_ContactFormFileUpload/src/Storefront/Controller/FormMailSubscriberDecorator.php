<?php

declare(strict_types=1);
/*
 * (c) shopware AG <info@shopware.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WebLa_ContactFormFileUpload\Storefront\Controller;

use Shopware\Core\Content\ContactForm\Event\ContactFormEvent;
use Shopware\Core\Content\Flow\Dispatching\Action\SendMailAction;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\Mail\Service\MailAttachmentsConfig;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Swag\CmsExtensions\Form\Action\FormMailSubscriber;
use Swag\CmsExtensions\Form\Aggregate\FormGroupField\FormGroupFieldCollection;
use Swag\CmsExtensions\Form\Aggregate\FormGroupField\FormGroupFieldEntity;
use Swag\CmsExtensions\Form\Aggregate\FormGroupField\Type\Email;
use Swag\CmsExtensions\Form\Event\CustomFormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormMailSubscriberDecorator implements EventSubscriberInterface
{
    public function __construct(
        private readonly FormMailSubscriber $decorated,
        private readonly SystemConfigService $systemConfigService,
        private readonly AbstractMailService $mailService
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            CustomFormEvent::EVENT_NAME => 'sendMail',
            ContactFormEvent::EVENT_NAME => 'sendMail',
        ];
    }

    public function getDecorated(): FormMailSubscriber
    {
        return $this->decorated;
    }

    public function sendMail(CustomFormEvent $event): void
    {

        if (!$event->getContext()->getExtension(SendMailAction::MAIL_CONFIG_EXTENSION)) {
            $this->getDecorated()->sendMail($event);
            return;
        }

        /** @var \Shopware\Core\Content\MailTemplate\Subscriber\MailSendSubscriberConfig */
        $ext = $event->getContext()->getExtension(SendMailAction::MAIL_CONFIG_EXTENSION);

        $receivers = $event->getForm()->getReceivers();

        if (empty($receivers)) {
            $receivers[] = $this->systemConfigService->get('core.basicInformation.email', $event->getSalesChannelId());
        }

        $groups = $event->getForm()->getGroups();
        $fields = $groups === null ? new FormGroupFieldCollection() : $groups->getFields();

        $data = [];
        $mailTemplate = $event->getForm()->getMailTemplate();
        if ($mailTemplate) {
            $data = $mailTemplate->jsonSerialize();
            $data['subject'] = $mailTemplate->getTranslation('subject');
            $data['senderName'] = $mailTemplate->getTranslation('senderName');
            $data['contentHtml'] = $mailTemplate->getTranslation('contentHtml');
            $data['contentPlain'] = $mailTemplate->getTranslation('contentPlain');
        }

        $data['mediaIds'] = $ext->getMediaIds();

        $data['salesChannelId'] = $event->getSalesChannelId();
        if ($sender = $this->getSenderMail($fields, $event->getFormData())) {
            $data['replyTo'] = $sender;
        }

        $templateData = [
            'form' => $event->getForm(),
            'fields' => $fields,
            'formData' => $event->getFormData(),
        ];

        foreach ($receivers as $mail) {
            if (!\is_string($mail)) {
                continue;
            }

            $data['recipients'] = [$mail => $mail];

            $this->mailService->send($data, $event->getContext(), $templateData);
        }
    }

    /**
     * @param array<string, string|null> $formData
     */
    private function getSenderMail(FormGroupFieldCollection $fieldCollection, array $formData): ?string
    {
        $mailField = $fieldCollection->filter(static fn(FormGroupFieldEntity $field) => $field->getType() === Email::NAME)->first();

        if ($mailField === null) {
            return null;
        }

        return $formData[$mailField->getTechnicalName()] ?? null;
    }
}
