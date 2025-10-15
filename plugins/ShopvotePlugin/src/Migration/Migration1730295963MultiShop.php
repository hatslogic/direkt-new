<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * @internal
 */
#[Package('core')]
class Migration1730295963MultiShop extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1730295963;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            ALTER TABLE `shopvote_reviews`
            ADD `shop_id` INT NOT NULL DEFAULT 0 AFTER `id`;
        ');

        $connection->executeStatement('
            ALTER TABLE `shopvote_reviews`
            ADD INDEX `shop_id_index` (`shop_id`);
        ');
    }
}
