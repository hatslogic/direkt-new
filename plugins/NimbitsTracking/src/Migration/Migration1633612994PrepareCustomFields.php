<?php declare(strict_types=1);

namespace Nimbits\NimbitsTracking\Migration;

use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Nimbits\NimbitsTracking\Util\CustomFieldHelper;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1633612994PrepareCustomFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1633612994;
    }

    public function update(Connection $connection): void
    {
        $fieldset = CustomFieldHelper::insertOrUpdateSecondUniqueRow($connection, 'custom_field_set', 'name', [
            'name' => 'nb_tracking',
            'config' => json_encode([
                "label" => ["de-DE" => "Nimbits Tracking", "en-GB" => "Nimbits Tracking"],
                "translated" => false
            ]),
            'active' => 1,
            'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);

        try
        {
            $connection->insert('custom_field_set_relation', [
                'id' => Uuid::randomBytes(),
                'set_id' => $fieldset['id'],
                'entity_name' => 'customer',
                'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } catch (Exception $e) {}//relation may or may not already exist, therefore no handling required

        try
        {
            $connection->insert('custom_field_set_relation', [
                'id' => Uuid::randomBytes(),
                'set_id' => $fieldset['id'],
                'entity_name' => 'order',
                'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
            ]);
        } catch (Exception $e) {}//relation may or may not already exist, therefore no handling required

        CustomFieldHelper::insertOrUpdateSecondUniqueRow($connection, 'custom_field', 'name', [
            'name' => 'nb_tracking_partner',
            'type' => 'text',
            'config' => json_encode([
                "label" => "Partner",
                "componentName" => "sw-text-field",
                "customFieldPosition" => 1,
                "customFieldType" => "text",
                "translated" => false
            ]),
            'active' => 1,
            'set_id' => $fieldset['id'],
            'created_at' => (new DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
