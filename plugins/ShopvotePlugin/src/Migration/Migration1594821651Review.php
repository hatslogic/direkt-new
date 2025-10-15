<?php declare(strict_types=1);

namespace Shopvote\ShopvotePlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1594821651Review extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1594821651;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
        CREATE TABLE IF NOT EXISTS `shopvote_reviews` (
        `id` BINARY(16) NOT NULL,
        `review_uid` VARCHAR(255) NOT NULL UNIQUE,
        `review_url` VARCHAR(255) NULL,
        `product_review_id` BINARY(16) NOT NULL,
        `created_at` DATETIME(3) NOT NULL,
        `updated_at` DATETIME(3) NULL,
        PRIMARY KEY (`id`, `product_review_id`),
        CONSTRAINT `fk.product_review.id` FOREIGN KEY (`product_review_id`)
            REFERENCES `product_review` (`id`) ON DELETE CASCADE ON UPDATE CASCADE )
        ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
