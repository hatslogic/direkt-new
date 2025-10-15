<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Struct;

use Shopware\Core\Framework\Struct\Struct;

class ReviewStruct extends Struct
{
    public $isShopvoteReview;
    public $reviewUrl;

    /**
     * @return mixed
     */
    public function getReviewUrl()
    {
        return $this->reviewUrl;
    }

    /**
     * @param mixed $reviewUrl
     */
    public function setReviewUrl($reviewUrl): void
    {
        $this->reviewUrl = $reviewUrl;
    }

    public function getIsShopvoteReview()
    {
        return $this->isShopvoteReview;
    }

    public function setIsShopvoteReview($isShopvoteReview)
    {
        $this->isShopvoteReview = $isShopvoteReview;
    }


}