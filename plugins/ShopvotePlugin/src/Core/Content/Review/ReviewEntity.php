<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Core\Content\Review;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ReviewEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var int
     */
    protected $shop_id;

    /**
     * @var string
     */
    protected $review_uid;

    /**
     * @var string
     */
    protected $product_review_id;

    /**
     * @var string
     */
    protected $review_url;

    /**
     * @return string
     */
    public function getShopId(): int
    {
        return $this->shop_id;
    }

    /**
     * @param string $shop_id
     */
    public function setShopId(string $shop_id): void
    {
        $this->shop_id = $shop_id;
    }

    /**
     * @return string
     */
    public function getReviewUid(): string
    {
        return $this->review_uid;
    }

    /**
     * @param string $review_uid
     */
    public function setReviewUid(string $review_uid): void
    {
        $this->review_uid = $review_uid;
    }

    /**
     * @return string
     */
    public function getProductReviewId(): string
    {
        return $this->product_review_id;
    }

    /**
     * @param string $product_review_id
     */
    public function setProductReviewId(string $product_review_id): void
    {
        $this->product_review_id = $product_review_id;
    }

    /**
     * @return string
     */
    public function getReviewUrl(): string
    {
        return $this->review_url;
    }

    /**
     * @param string $review_url
     */
    public function setReviewUrl(string $review_url): void
    {
        $this->review_url = $review_url;
    }
}