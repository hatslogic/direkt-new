<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Core\Content\Review;

use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ReviewDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'shopvote_reviews';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ReviewEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ReviewCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new IntField('shop_id', 'shop_id'))->addFlags(new Required()),
            (new StringField('review_uid', 'review_uid'))->addFlags(new Required()),
            (new StringField('review_url', 'review_url'))->addFlags(new Required()),
            (new FkField('product_review_id', 'productReviewId', ProductReviewDefinition::class))->addFlags(new Required())
        ]);
    }
}
