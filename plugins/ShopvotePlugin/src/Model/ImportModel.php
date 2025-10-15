<?php

namespace Shopvote\ShopvotePlugin\Model;

use Exception;
use Shopvote\ShopvotePlugin\Components\ImportService;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class ImportModel
{
    /** @var ImportService */
    private $importService;

    /** @var  */
    private $writer;

    /** @var SystemConfigService */
    private $systemConfigService;

    /**
     * ImportService constructor.
     * @param SystemConfigService $systemConfigService
     */
    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    /**
     * @required
     * @param ImportService $importService
     */
    public function setImportService(ImportService $importService): void
    {
        $this->importService = $importService;
    }

    /**
     * @param $writer
     */
    public function setWriter($writer)
    {
        $this->writer = $writer;
    }

    /**
     * @param $msg
     */
    public function echoOutput($msg)
    {
        if ($this->writer){
            $this->writer->writeln($msg);
        }
    }

    /**
     * @param Context $context
     */
    public function importAll(Context $context)
    {
        $availableSalesChannelIds = $this->importService->getAvailableSalesChannelIds($context);

        if (empty($availableSalesChannelIds)) {
            $this->echoOutput("<info>All active sales channels have import disabled via config<info>");
            return;
        }

        foreach ($availableSalesChannelIds as $salesChannelId) {
            try {
                $reviews = $this->importService->getReviewsForLastNumberOfDays($context, $salesChannelId);
            }
            catch (Exception $e) {
                $this->echoOutput('<error>Import for sales channel '.$salesChannelId.' did fail. Reason: '.$e->getMessage().'<error>');
                return;
            }

            $this->importReviews($reviews, $context, $salesChannelId);
        }
    }

    /**
     * @param $reviews
     * @param Context $context
     * @param string|null $salesChannelId
     */
    public function importReviews($reviews, Context $context, ?string $salesChannelId = null)
    {
        if (!$this->isImportEnabled($salesChannelId)) {
            $this->echoOutput("<info>Productreview Import is disabled in config<info>");
        }

        foreach ($reviews as $review) {
            try {
                $this->importService->addReviewToDatabase($review, $context, $salesChannelId);
                $this->echoOutput(sprintf("<info> reviewUID: %s added for ordernumber: %s</info>", $review->reviewUID, $review->sku));

            } catch (Exception $exception) {
                $this->echoOutput(sprintf("<comment>%s</comment>", $exception->getMessage()));
            }
        }
    }

    /**
     * @param string|null $salesChannelId
     * @return array|mixed|null
     */
    public function isImportEnabled(?string $salesChannelId = null)
    {
        return $this->systemConfigService->get("ShopvotePlugin.config.importReviews", $salesChannelId);
    }
}