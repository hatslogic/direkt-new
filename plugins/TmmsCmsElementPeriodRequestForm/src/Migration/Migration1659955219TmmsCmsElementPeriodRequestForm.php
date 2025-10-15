<?php
declare(strict_types=1);

namespace Tmms\CmsElementPeriodRequestForm\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1659955219TmmsCmsElementPeriodRequestForm extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1659955219;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
        ALTER TABLE `periodrequestform`
        MODIFY COLUMN comment LONGTEXT;');

        $connection->executeStatement('
        ALTER TABLE `periodrequestform`
        MODIFY COLUMN freeinput LONGTEXT;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
