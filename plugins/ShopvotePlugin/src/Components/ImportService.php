<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Components;

use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin\PluginService;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ImportService
{
    const APIAUTH              = "https://api.shopvote.de/auth";
    const APIREVIEW            = 'https://api.shopvote.de/product-reviews/v2/reviews';
    const CONFIG_KEY_TEMPLATE  = 'ShopvotePlugin.config.%s';
    const SYNCDAYS_INCREMENTAL = 7;
    const SYNCDAYS_FULL        = 365;

    /** @var SystemConfigService */
    private $systemConfigService;

    /** @var CustomerDummyService */
    private $customerDummyService;

    /** @var string */
    private $reviewpage;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var EntityRepository */
    private $productRepository;

    /** @var EntityRepository */
    private $productReviewRepository;

    /** @var EntityRepository */
    private $salesChannelRepository;

    /** @var EntityRepository */
    private $shopvoteRepository;

    /** @var PluginService */
    private $pluginService;

    /**
     * ImportService constructor.
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(
        SystemConfigService $systemConfigService,
        LoggerInterface $logger,
        EntityRepository $productRepository,
        EntityRepository $productReviewRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $shopvoteRepository,
        PluginService $pluginService
    ) {
        $this->systemConfigService     = $systemConfigService;
        $this->logger                  = $logger;
        $this->productRepository       = $productRepository;
        $this->productReviewRepository = $productReviewRepository;
        $this->salesChannelRepository  = $salesChannelRepository;
        $this->shopvoteRepository      = $shopvoteRepository;
        $this->pluginService           = $pluginService;
    }

    /**
     * @param CustomerDummyService $customerDummyService
     */
    public function setCustomerDummyService(CustomerDummyService $customerDummyService)
    {
        $this->customerDummyService = $customerDummyService;
    }

    /**
     * @param string|null $salesChannelId
     * @return mixed
     */
    public function getAuthorization(?string $salesChannelId = null)
    {
        $token = json_decode($this->curlAuth($salesChannelId));

        if (!isset($token->Token)) {
            return false;
        }

        return $token->Token;
    }

    /**
     * @param Context $context
     * @param int $shopvoteShopId
     * @return bool
     * @throws Zend_Db_Adapter_Exception
     * @throws Zend_Db_Statement_Exception
     */
    public function isFirstSync(Context $context, int $shopvoteShopId)
    {
        $plugin        = $this->pluginService->getPluginByName('ShopvotePlugin', $context);
        $pluginVersion = $plugin->getVersion();

        $criteria = new Criteria();

        if (version_compare($pluginVersion, '1.2.1', '>')) {
            $criteria->addFilter(new EqualsFilter('shop_id', $shopvoteShopId));
        }

        $entities = $this->shopvoteRepository->search($criteria, $context);

        if ($entities->getTotal() > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * We ask the API to look back this many days into the past
     *
     * @param Context $context
     * @param int $shopId
     * @return int
     */
    private function getSyncDays(Context $context, int $shopId): int
    {
        $days = self::SYNCDAYS_INCREMENTAL;
        if ($this->isFirstSync($context, $shopId)) {
            $days = self::SYNCDAYS_FULL;
        }
        return $days;
    }

    /**
     * @param string $key
     * @return string
     */
    private function getConfigKey(string $key): string
    {
        return sprintf(self::CONFIG_KEY_TEMPLATE, $key);
    }

    /**
     * @param Context $context
     * @param string|null $salesChannelId
     */
    public function getReviewsForLastNumberOfDays(Context $context, ?string $salesChannelId = null)
    {
        $shopvoteShopId = (int) $this->systemConfigService->get($this->getConfigKey('shopId'), $salesChannelId);

        $days = $this->getSyncDays($context, $shopvoteShopId);
        $token = $this->getAuthorization($salesChannelId);

        if ($token === false) {
            throw new Exception('Auth token generation failure!');
        }

        $output = json_decode($this->curlReview($token, $days, $salesChannelId));

        $this->reviewpage = $output->reviewpage;
        return $output->reviews;
    }

    /**
     * @param string|null $salesChannelId
     * @return bool|string
     */
    public function curlAuth(?string $salesChannelId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::APIAUTH);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getAuthHeader($salesChannelId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $tokenObject = curl_exec($ch);

        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            echo "cURL error ({$errno}):\n {$error_message}";
        }
        curl_close($ch);

        return $tokenObject;
    }

    /**
     * @param $tokenObject
     * @param $sku
     * @param string|null $salesChannelId
     * @return bool|string
     */
    public function curlReview($tokenObject, $days, ?string $salesChannelId = null)
    {
        $apiUrl = sprintf("%s?days=%d", self::APIREVIEW, $days);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getReviewHeader($tokenObject, $salesChannelId));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        /**
         * Checking for curl-Errors
         */
        if ($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            $this->logger->error("cURL error ({$errno}):\n {$error_message}");
        }
        curl_close($ch);

        return $output;
    }

    /**
     * @return array
     */
    public function getAuthHeader(?string $salesChannelId = null)
    {
        $shopId    = $this->systemConfigService->get($this->getConfigKey('shopId'), $salesChannelId);
        $apiKey    = $this->systemConfigService->get($this->getConfigKey('apiKey'), $salesChannelId);
        $apiSecret = $this->systemConfigService->get($this->getConfigKey('apiKeySecret'), $salesChannelId);

        return $headers = [
            'Apikey: ' . $apiKey,
            'Apisecret: ' . $apiSecret,
            'User-Agent:  App.GDX.' . $shopId
        ];
    }

    /**
     * @param $token
     * @param string|null $salesChannelId
     * @return array
     */
    public function getReviewHeader($token, ?string $salesChannelId = null)
    {
        $shopId = $this->systemConfigService->get($this->getConfigKey('shopId'), $salesChannelId);

        return [
            'User-Agent: App.GDX.' . $shopId,
            'Token: Bearer ' . $token
        ];
    }

    /**
     * @param string|null $salesChannelId
     * @return string
     */
    public function setHeadlineLength($text, ?string $salesChannelId = null)
    {
        $length = $this->systemConfigService->get($this->getConfigKey('headlineLength'), $salesChannelId);

        if (($length > 0) && ($length < mb_strlen($text))) {
            $headline = mb_substr($text, 0, $length);
        } else {
            $headline = mb_substr($text, 0, 15);
        }

        return $headline;
    }

    /**
     * Check if we already created the shopvote review record.
     *
     * @param $reviewUID
     * @return bool
     */
    public function checkReviewUID($reviewUID)
    {
        $entities = $this->shopvoteRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('review_uid', $reviewUID)),
            Context::createDefaultContext()
        );

        if ($entities->getTotal() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Load product by product number.
     *
     * @param $sku
     * @return mixed|null
     * @throws Exception
     */
    public function getArticleID($sku)
    {
        $entities = $this->productRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('productNumber', $sku)),
            Context::createDefaultContext()
        );

        foreach ($entities as $entity) {
            $output = $entity->get('id');
        }

        if (isset($output)) {
            return $output;
        } else {
            return FALSE;
        }
    }

    /**
     * Get the current Sales Channel ID
     *
     * @return mixed
     */
    public function getActiveSalesChannelId()
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', 1));
        $criteria->setLimit(1);

        $entity = $this->salesChannelRepository->search($criteria,
            Context::createDefaultContext())->getEntities()->first();
        return $entity->get("id");
    }

    /**
     * Create shopware native product review.
     *
     * @param $review
     * @param $productID
     * @param $headline
     * @param $customerId
     * @return string
     * @throws Exception
     */
    public function insertInProductReviews($review, $productID, $headline, $customerId)
    {
        $salesChannelId = $this->getActiveSalesChannelId();

        $id = Uuid::randomHex();

        $this->productReviewRepository->create(
            [
                [
                    'id' => $id, 'externalUser' => $review->author, 'productId' => $productID,
                    'title' => $headline, 'content' => $review->text, 'points' => floatval($review->rating_value),
                    'createdAt' => $review->created, 'updatedAt' => $review->created, 'status' => true,
                    'salesChannelId' => $salesChannelId, 'customerId' => $customerId
                ],
            ],
            Context::createDefaultContext()
        );

        return $id;
    }

    /**
     * Create mapping in shopvote table.
     *
     * @param Context $context
     * @param $review
     * @param $id
     * @param $reviewUrl
     * @param $shopId
     */
    public function insertInShopvoteReviews(Context $context, $review, $id, $reviewUrl, $shopId)
    {
        $plugin        = $this->pluginService->getPluginByName('ShopvotePlugin', $context);
        $pluginVersion = $plugin->getVersion();

        $data = [
            'review_uid'      => $review->reviewUID,
            'productReviewId' => $id,
            'review_url'      => $reviewUrl
        ];

        if (version_compare($pluginVersion, '1.2.1', '>')) {
            $data['shop_id'] = $shopId;
        }

        $this->shopvoteRepository->create([$data], $context);
    }

    /**
     * @param $reciew
     * @param string|null $salesChannelId
     */
    public function addReviewToDatabase($review, Context $context, ?string $salesChannelId = null)
    {
        if ($this->checkReviewUID($review->reviewUID)) {
            $error = sprintf("reviewUID: %s for ordernumber: %s already exists", $review->reviewUID, $review->sku);
            throw new \Exception($error);
        }

        if ($articleId = $this->getArticleID($review->sku)) {
            $customerId = $this->customerDummyService->getShopvoteUserID();
            $headline   = $this->setHeadlineLength($review->text, $salesChannelId);
            $id         = $this->insertInProductReviews($review, $articleId, $headline, $customerId);
            $shopId     = $this->systemConfigService->get($this->getConfigKey('shopId'), $salesChannelId);

            $this->insertInShopvoteReviews($context, $review, $id, $this->reviewpage, $shopId);
        }
        else {
            $error = sprintf("articleID: not found for ordernumber: %s", $review->sku);
            throw new \Exception($error);
        }

    }

    /**
     * @param Context $context
     * @return (string|null)[]
     */
    public function getAvailableSalesChannelIds(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', 1));

        $salesChannelSearchResult = $this->salesChannelRepository->search($criteria, $context);

        $salesChannelIds = [];

        if ($this->systemConfigService->get($this->getConfigKey('importReviews'))) {
            $salesChannelIds[] = null;
        }

        foreach($salesChannelSearchResult as $salesChannel) {
            if ($this->systemConfigService->get($this->getConfigKey('importReviews'), $salesChannel->getUniqueIdentifier())) {
                $salesChannelIds[] = $salesChannel->getUniqueIdentifier();
            }
        }

        return $salesChannelIds;
    }
}
