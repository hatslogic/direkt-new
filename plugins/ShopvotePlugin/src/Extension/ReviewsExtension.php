<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Extension;

use Shopware\Core\Content\Product\Aggregate\ProductReview\ProductReviewDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Runtime;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ObjectField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ReviewsExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            (new ObjectField('shopvote_review_details', 'shopvote_review_details'))->addFlags(new Runtime())
        );
    }

    public function getDefinitionClass(): string
    {
        return ProductReviewDefinition::class;
    }
}