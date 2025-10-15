<?php

namespace Nimbits\NimbitsTracking\Subscriber;

use Nimbits\NimbitsTracking\Service\SettingService;
use Nimbits\NimbitsTracking\Service\VisitorCounterService;
use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Customer\Event\CustomerLoginEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Storefront\Pagelet\Footer\FooterPageletLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class PartnerSubscriber implements EventSubscriberInterface
{
    private VisitorCounterService $visitorCounterService;
    private SettingService $settings;
    private EntityRepository $customerRepository;
    private EntityRepository $orderRepository;
    private RequestStack $requestStack;

    public function __construct(
        VisitorCounterService       $visitorCounterService,
        SettingService              $settingService,
        EntityRepository            $customerRepository,
        EntityRepository            $orderRepository,
        RequestStack                $requestStack
    )
    {
        $this->visitorCounterService = $visitorCounterService;
        $this->settings = $settingService;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
            CustomerLoginEvent::class => 'onCustomerLogin',
            CheckoutOrderPlacedEvent::class => 'onOrderPlaced',
            FooterPageletLoadedEvent::class => 'onFooterLoaded'
        ];
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $this->visitorCounterService->countVisit($this->requestStack->getCurrentRequest(), $this->getPartner());

        if (!empty($this->settings->getSetting('parameterName'))) {
            $partner = $event->getRequest()->query->get($this->settings->getSetting('parameterName'));

            if (!empty($partner)) {
                if (empty($event->getRequest()->getSession()->get('nb_tracking_partner')) || $this->settings->getSetting('overrideSession')) {
                    $event->getRequest()->getSession()->set('nb_tracking_partner', $partner);
                }

                if ($this->isCookieAllowed()) {
                    if (empty($event->getRequest()->cookies->get($this->settings->getSetting('cookieName'))) || $this->settings->getSetting('overrideSession')) {
                        $event->getResponse()->headers->setCookie(new Cookie(
                            $this->settings->getSetting('cookieName'), $partner,
                            time() + substr($this->settings->getSetting('cookieDuration'), 1)
                        ));
                    }
                }
            } else {
                if (
                    $this->isCookieAllowed() &&
                    !empty($event->getRequest()->getSession()->get('nb_tracking_partner'))
                    && empty($event->getRequest()->cookies->get($this->settings->getSetting('cookieName')))
                ) {
                    $event->getResponse()->headers->setCookie(new Cookie(
                        $this->settings->getSetting('cookieName'), $event->getRequest()->getSession()->get('nb_tracking_partner'),
                        time() + substr($this->settings->getSetting('cookieDuration'), 1)
                    ));
                } else if (
                    !empty($event->getRequest()->cookies->get($this->settings->getSetting('cookieName')))
                    && empty($event->getRequest()->getSession()->get('nb_tracking_partner'))
                ) {
                    $event->getRequest()->getSession()->set('nb_tracking_partner', $event->getRequest()->cookies->get($this->settings->getSetting('cookieName')));
                }
            }
        }
    }

    public function onCustomerLogin(CustomerLoginEvent $event)
    {
        // Filter for registration
        if (!empty($event->getCustomer()->getLastLogin())) {
            return;
        }

        $partner = $this->getPartner();

        if (!empty($partner)) {
            $customer = $event->getCustomer();

            $this->customerRepository->update([[
                'id' => $customer->getId(),
                'customFields' => array_merge(
                    $customer->getCustomFields() ?? [],
                    ['nb_tracking_partner' => $partner]
                )
            ]], $event->getContext());
        }
    }

    public function onOrderPlaced(CheckoutOrderPlacedEvent $event)
    {
        $partner = $this->getPartner();

        if (!empty($partner)) {
            $order = $event->getOrder();

            $this->orderRepository->update([[
                'id' => $order->getId(),
                'customFields' => array_merge(
                    $order->getCustomFields() ?? [],
                    ['nb_tracking_partner' => $partner]
                )
            ]], $event->getContext());
        }
    }

    private function isCookieAllowed(): bool
    {
        return ($this->settings->getSetting('enableCookie') ?? true) &&
            ($this->settings->getSetting('cookieType') == 'cookie.groupRequired' || !empty($this->requestStack->getCurrentRequest()->cookies->get($this->settings->getSetting('cookieName') . '_enabled')));
    }

    private function getPartner(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        return $request->getSession()->get('nb_tracking_partner') ?? $request->cookies->get($this->settings->getSetting('cookieName')) ?? null;
    }

    public function onFooterLoaded(FooterPageletLoadedEvent $event)
    {
        $event->getPagelet()->addExtension('nimbitsTracking',
            $this->settings->getSettingsAsStruct($event->getSalesChannelContext()->getSalesChannel()->getId())

        );
    }
}