<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Subscriber;

use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopvote\ShopvotePlugin\Struct\ReviewStruct;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Content\Product\ProductEvents;

class ReviewSubscriber implements EventSubscriberInterface
{
    private $shopvoteReviewsRepository;

    public function __construct(EntityRepository $shopvoteReviewsRepository)
    {
        $this->shopvoteReviewsRepository = $shopvoteReviewsRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_REVIEW_LOADED => 'onProductReviewLoaded'
        ];
    }

    public function onProductReviewLoaded(EntityLoadedEvent $event): void
    {
        /** @var ProductReviewEntity $productReviewEntity */
        foreach ($event->getEntities() as $productReviewEntity) {
            $struct = new ReviewStruct();
            $struct->setIsShopvoteReview($this->isReviewLinkedToShopvoteReview($productReviewEntity));
            $struct->setReviewUrl($this->getUrlForReview($productReviewEntity));

            $productReviewEntity->addExtension('shopvote_review_details', $struct);
        }
    }

    private function isReviewLinkedToShopvoteReview(ProductReviewEntity $productReviewEntity)
    {
        $customIds = $this->shopvoteReviewsRepository->searchIds((
            new Criteria())->addFilter(new EqualsFilter('productReviewId', $productReviewEntity->getId())),
            Context::createDefaultContext()
        );

        if ($customIds->getTotal() > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function getUrlForReview(ProductReviewEntity $productReviewEntity)
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productReviewId', $productReviewEntity->getId()));
        $criteria->setLimit(1);

        $entity = $this->shopvoteReviewsRepository->search($criteria,
            Context::createDefaultContext())->getEntities()->first();

        if ($entity) {
            return $entity->get("review_url");
        } else {
            return false;
        }
    }
}