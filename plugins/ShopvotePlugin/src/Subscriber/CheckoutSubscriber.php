<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Subscriber;

use Exception;
use Shopvote\ShopvotePlugin\Struct\TransferStruct;
use Shopware\Core\Checkout\Order\Aggregate\OrderLineItem\OrderLineItemEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Page\Checkout\Finish\CheckoutFinishPageLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\Struct\Struct;

class CheckoutSubscriber implements EventSubscriberInterface
{
    /** @var EntityRepository */
    private $productRepository;

    /** @var SystemConfigService */
    private $systemConfigService;

    /** @var TransferStruct */
    private $transferStruct;

    /**
     * CheckoutSubscriber constructor.
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        EntityRepository $productRepository,
        SystemConfigService $systemConfigService)
    {
        $this->productRepository   = $productRepository;
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents() :array
    {
        return [
            CheckoutFinishPageLoadedEvent::class  => ['checkoutFinishPageLoaded', 1],
        ];
    }

    /**
     * Get a product by id.
     *
     * @param $id
     * @return ProductEntity
     * @throws Exception
     */
    public function getArticle($id)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('id', $id));
        $criteria->setLimit(1);

        return $this->productRepository->search($criteria,
            Context::createDefaultContext())->getEntities()->first();
    }

    /**
     * @param CheckoutFinishPageLoadedEvent $event
     * @throws Exception
     */
    public function checkoutFinishPageLoaded(CheckoutFinishPageLoadedEvent $event)
    {
        $this->transferStruct = new TransferStruct();

        /**
         * Filling Custom Struct with data
         */
        $this->transferStruct->setApiKey($this->systemConfigService->get("ShopvotePlugin.config.apiKey", $event->getContext()->getSource()->getSalesChannelId()));
        $this->transferStruct->setCustomerMail($event->getSalesChannelContext()->getCustomer()->getEmail());
        $this->transferStruct->setOrderNumber($event->getPage()->getOrder()->getOrderNumber());

        /**
         * Changing from OrderLineItemEntity to ProductEntity
         */
        /** @var OrderLineItemEntity $lineItem */
        foreach ($event->getPage()->getOrder()->getLineItems() as $lineItem) {
            if ($lineItem->getType() === 'product') {
				$productEntity = $this->getArticle($lineItem->getProductId());
                $coverImage = $lineItem->getCover();
				if ($coverImage instanceof Struct) {
                    $productEntity->addExtension('cover', $coverImage);
                }
				$this->transferStruct->addEntity($productEntity);
			}
        }
        /** Sending data to frontend */
        $event->getPage()->addExtension('TransferStruct', $this->transferStruct);
    }
}
