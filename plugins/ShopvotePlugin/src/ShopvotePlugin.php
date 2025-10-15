<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin;

use Doctrine\DBAL\Connection;
use Shopvote\ShopvotePlugin\Components\CustomerDummyService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;

class ShopvotePlugin extends Plugin
{
    /** @var CustomerDummyService */
    private $customerDummyService;

    /**
     * @param CustomerDummyService $customerDummyService
     * @required
     */
    public function setCustomerDummyService(CustomerDummyService $customerDummyService): void
    {
        $this->customerDummyService = $customerDummyService;
    }

    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
        $oldPluginVersion = $updateContext->getCurrentPluginVersion();

        if (version_compare($oldPluginVersion, '1.2.3', '<')) {
            $systemConfigService = $this->container->get('Shopware\Core\System\SystemConfig\SystemConfigService');

            $coreShopId = $systemConfigService->get('ShopvotePlugin.config.shopId');

            $this->updateUnassignedShopvoteReviewsToShopId($coreShopId);
        }
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);

        if (!$this->checkForShopvoteUser()) {
            $this->customerDummyService->addShopvoteCustomer();
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $this->removeConfiguration($uninstallContext->getContext());
        $this->deleteShopvoteUser($uninstallContext->getContext());
        $this->cleanUpDbUponUninstall($uninstallContext->getContext());
    }

    public function removeConfiguration($context): void
    {
        /** @var EntityRepository $systemConfigRepository */
        $systemConfigRepository = $this->container->get('system_config.repository');
        $criteria = (new Criteria())->addFilter(new ContainsFilter('configurationKey', $this->getName() . '.config.'));
        $idSearchResult = $systemConfigRepository->searchIds($criteria, $context);

        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $idSearchResult->getIds());

        if ($ids === []) {
            return;
        }

        $systemConfigRepository->delete($ids, $context);
    }

    /**
     * Assign main shop id in config to unassigned reviews
     *
     * @param $shopId
     */
    public function updateUnassignedShopvoteReviewsToShopId($shopId): void
    {
        $shopvoteRepository = $this->container->get('shopvote_reviews.repository');

        $context = Context::createDefaultContext();

        $searchResult = $shopvoteRepository->searchIds(
            (new Criteria())->addFilter(new EqualsFilter('shop_id', 0)),
            $context
        );

        $updates = [];

        foreach ($searchResult->getData() as $result) {
            $updates[] = [
                'id' => $result['id'],
                'shop_id' => $shopId,
            ];
        }

        $shopvoteRepository->update($updates, $context);
    }

    /**
     * @param Context $context
     */
    public function deleteShopvoteUser($context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('firstName', 'SHOPVOTE'));
        $criteria->setLimit(1);

        $idSearchResult = $this->getCustomerRepository()->searchIds($criteria, $context);
        $ids = array_map(static function ($id) {
            return ['id' => $id];
        }, $idSearchResult->getIds());

        if ($ids === []) {
            return;
        }

        $this->getCustomerRepository()->delete($ids, Context::createDefaultContext());
    }

    public function checkForShopvoteUser()
    {
        $entities = $this->getCustomerRepository()->search(
            (new Criteria())->addFilter(new EqualsFilter('firstName', "SHOPVOTE")),
            Context::createDefaultContext()
        );

        if($entities->getTotal() > 0) {
            return true;
        } else {
            return false;
        }
    }

    private function getCustomerRepository()
    {
        return $this->container->get('customer.repository');
    }

    private function cleanUpDbUponUninstall($context)
    {
        $this->removeShopvoteReviews($context);
        $this->deleteShopvoteReviewTable();
    }

    private function removeShopvoteReviews($context)
    {
        $connection = $this->container->get(Connection::class);
        $connection->executeUpdate('DELETE product_review FROM product_review JOIN shopvote_reviews ON product_review.id = shopvote_reviews.product_review_id');
    }

    private function deleteShopvoteReviewTable()
    {
        $connection = $this->container->get(Connection::class);
        $connection->executeUpdate('DROP TABLE IF EXISTS `shopvote_reviews`');
    }
}
