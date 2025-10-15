<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Core\Content\Review;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void              add(ReviewEntity $entity)
 * @method void              set(string $key, ReviewEntity $entity)
 * @method ReviewEntity[]    getIterator()
 * @method ReviewEntity[]    getElements()
 * @method ReviewEntity|null get(string $key)
 * @method ReviewEntity|null first()
 * @method ReviewEntity|null last()
 */
class ReviewCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ReviewEntity::class;
    }
}