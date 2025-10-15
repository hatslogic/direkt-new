<?php declare(strict_types=1);

namespace Nimbits\NimbitsTracking\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1639139910CreateVisitorCountTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1639139910;
    }

    public function update(Connection $connection): void
    {
        try {
            $connection->executeStatement(<<<SQL
CREATE TABLE `nb_tracking_visitor_ips` (
    `day`           DATE            NOT NULL,
    `ip`            BINARY(16)      NOT NULL,
    `referrer`      VARCHAR(255)    NOT NULL,
    `requests`      INT             NOT NULL,
    PRIMARY KEY (`day`, `ip`)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_ci;
SQL
            );
        } catch (Exception $e) {
        }

        try {
            $connection->executeStatement(<<<SQL
CREATE TABLE `nb_tracking_visitors` (
    `day`       DATE            NOT NULL,
    `referrer`  VARCHAR(255)    NOT NULL,
    `visitors`  INT             NOT NULL,
    `requests`  INT             NOT NULL,
    PRIMARY KEY (`day`, `referrer`)
)
ENGINE = InnoDB
DEFAULT CHARSET = utf8mb4
COLLATE = utf8mb4_unicode_ci;
SQL
            );
        } catch (Exception $e) {
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
