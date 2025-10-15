<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Storefront\Page\GenericPageLoadedEvent;
use Shopware\Core\Framework\Struct\ArrayEntity;
use Shopware\Core\System\SystemConfig\SystemConfigService;


class BadgeSubscriber implements EventSubscriberInterface
{
    /** @var SystemConfigService */
    private $systemConfigService;

    /**
     * CheckoutSubscriber constructor.
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

	public static function getSubscribedEvents(): array
    {
        return [
            GenericPageLoadedEvent::class => 'onGenericPageLoaded'
        ];
    }

    public function onGenericPageLoaded (GenericPageLoadedEvent $event)
    {
        //declare an array
        $array = [
            'BadgeStatus' => $this->systemConfigService->get('ShopvotePlugin.config.shopvoteShowBadge', $event->getSalesChannelContext()->getSalesChannel()->getId()),
            'GraficsCode' => $this->systemConfigService->get('ShopvotePlugin.config.graficsCode', $event->getSalesChannelContext()->getSalesChannel()->getId()),
        ];

        //assign the array to the page
        $event->getPage()->assign($array);

        //add the array to the page as an extension
        $event->getPage()->addExtension('BadgeExtension', new ArrayEntity($array));

		//echo "Code: ".$this->systemConfigService->get('ShopvotePlugin.config.graficsCode');


    }
}

?>