<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1664978302TmmsCmsElementPeriodRequestForm extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1664978302;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `periodrequestform` (
                `id` BINARY(16) NOT NULL,
                `salutation` VARCHAR(255) NULL,
                `firstname` VARCHAR(255) NULL,
                `lastname` VARCHAR(255) NULL,
                `street` VARCHAR(255) NULL,
                `zipcode` VARCHAR(255) NULL,
                `city` VARCHAR(255) NULL,
                `country` VARCHAR(255) NULL,
                `email` VARCHAR(255) NULL,
                `phone` VARCHAR(255) NULL,
                `comment` LONGTEXT NULL,
                `date` VARCHAR(255) NULL,
                `freeinputlabel` VARCHAR(255) NULL,
                `freeinput` LONGTEXT NULL,
                `origin` VARCHAR(255) NULL,
                `originid` VARCHAR(255) NULL,
                `originname` VARCHAR(255) NULL,
                `confirmed` TINYINT(1) NULL DEFAULT "0",
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput2label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput2 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput3label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput3 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput4label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput4 LONGTEXT;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
