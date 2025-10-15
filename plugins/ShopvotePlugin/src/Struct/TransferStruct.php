<?php

namespace Shopvote\ShopvotePlugin\Struct;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Struct\Struct;

class TransferStruct extends Struct
{
    /** @var String */
    protected $apiKey;

    /** @var String */
    protected $customerMail;

    /** @var Int */
    protected $orderNumber;

    /** @var ProductEntity[] */
    protected $entities;

	/** @var String */
    protected $productUrl;

	/** @var String */
    protected $sku;

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param mixed $apiKey
     */
    public function setApiKey($apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return mixed
     */
    public function getCustomerMail()
    {
        return $this->customerMail;
    }

    /**
     * @param mixed $customerMail
     */
    public function setCustomerMail($customerMail): void
    {
        $this->customerMail = $customerMail;
    }

    /**
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param mixed $orderNumber
     */
    public function setOrderNumber($orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return ProductEntity[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @param ProductEntity $entities
     */
    public function setEntities(ProductEntity $entities): void
    {
        $this->entities = $entities;
    }

    /**
     * @return String[]
     */
    public function getProductUrl(): string
    {
        return $this->productUrl;
    }

    /**
     * @param String[] $productUrl
     */
    public function setProductUrl(array $productUrl): void
    {
        $this->productUrl = $productUrl;
    }

    /**
     * @return String[]
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param String[] $sku
     */
    public function setSku(array $sku): void
    {
        $this->sku = $sku;
    }

    /**
     * @param ProductEntity $entity
     */
    public function addEntity($entity)
    {
        $this->entities[] = $entity;
    }

}
