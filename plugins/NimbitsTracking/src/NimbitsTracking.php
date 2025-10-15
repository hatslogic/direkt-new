<?php

namespace Nimbits\NimbitsTracking;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class NimbitsTracking extends Plugin
{

    /**
     * @param \Shopware\Core\Framework\Plugin\Context\UninstallContext $uninstallContext
     * @throws \Doctrine\DBAL\Exception
     */
    public function uninstall(UninstallContext $uninstallContext): void
    {
        if (!$uninstallContext->keepUserData()) {
            /** @var Connection $connection */
            $connection = $this->container->get(Connection::class);

            $connection->executeUpdate('DELETE FROM custom_field_set WHERE name = ?', ['nb_tracking']);
            $this->cleanupCustomFields($connection, 'customer', 'nb_tracking_partner');
            $this->cleanupCustomFields($connection, 'order', 'nb_tracking_partner');

            try {
                $connection->executeUpdate('DROP TABLE nb_tracking_visitor_ips');
                $connection->executeUpdate('DROP TABLE nb_tracking_visitors');
            } catch (Exception $e) {}
        }

        $this->removeMigrations();
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @noinspection PhpSameParameterValueInspection
     */
    private function cleanupCustomFields(Connection $connection, string $tableName, string $fieldName)
    {
        $entries = $connection->executeQuery('SELECT * FROM `' . $tableName . '` WHERE custom_fields LIKE ?', ['%' . $fieldName . '%'])->fetchAll();

        foreach ($entries as $entry) {
            $fields = json_decode($entry['custom_fields'], true);

            if (in_array('nb_tracking_partner', $fields)) {
                unset($fields['nb_tracking_partner']);
            }

            $entry['custom_fields'] = json_encode($fields);

            $connection->executeUpdate("
                UPDATE `$tableName` SET
                    custom_fields = ?
                WHERE id = ?
            ", [
                $entry['custom_fields'],
                $entry['id']
            ]);
        }
    }
}