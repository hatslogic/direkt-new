<?php

namespace Nimbits\NimbitsTracking\Util;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Shopware\Core\Framework\Uuid\Uuid;

class CustomFieldHelper
{
    public static function selectRowByFields(Connection $connection, string $tableName, array $fields)
    {
        $params = [];
        $types = [];
        $where = [];

        foreach ($fields as $k => $v) {
            $params[$k] = $v;
            $types[':' . $k] = Types::STRING;
            $where[] = " " . $k . " = :" . $k . " ";
        }

        $where = implode(" AND ", $where);

        return $connection->fetchAssociative("SELECT * FROM " . $tableName . " WHERE " . $where, $params, $types);
    }

    public static function insertOrUpdateSecondUniqueRow(Connection $connection, string $tableName, string $unqiueFieldName, array $data) :array
    {
        $existant = self::selectRowByFields($connection, $tableName, [$unqiueFieldName => $data[$unqiueFieldName]]);

        if($existant === false)
        {
            $data['id'] = Uuid::randomBytes();
            $connection->insert($tableName, $data);
        }
        else
        {
            $ident = [$unqiueFieldName => $data[$unqiueFieldName]];
            $connection->update($tableName, $data, $ident);
        }

        return self::selectRowByFields($connection, $tableName, [$unqiueFieldName => $data[$unqiueFieldName]]);
    }
}