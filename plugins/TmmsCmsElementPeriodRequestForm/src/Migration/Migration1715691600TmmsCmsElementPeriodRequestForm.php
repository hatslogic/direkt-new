<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1715691600TmmsCmsElementPeriodRequestForm extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1715691600;
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
                `freeinput2label` VARCHAR(255) NULL,
                `freeinput2` LONGTEXT NULL,
                `freeinput3label` VARCHAR(255) NULL,
                `freeinput3` LONGTEXT NULL,
                `freeinput4label` VARCHAR(255) NULL,
                `freeinput4` LONGTEXT NULL,
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
            ADD freeinput5label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput5 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput6label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput6 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput7label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput7 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput8label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput8 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput9label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput9 LONGTEXT;');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput10label VARCHAR(255);');

        $connection->executeStatement('
            ALTER TABLE `periodrequestform`
            ADD freeinput10 LONGTEXT;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
