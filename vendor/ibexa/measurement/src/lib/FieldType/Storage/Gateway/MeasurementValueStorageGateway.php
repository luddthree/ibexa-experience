<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\FieldType\Storage\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Ibexa\Measurement\FieldType\Storage\MeasurementValueStorageGatewayInterface;
use Ibexa\Measurement\Storage\Gateway\Definition\TypeGateway;
use Ibexa\Measurement\Storage\Gateway\Definition\UnitGateway;
use Ibexa\Measurement\Storage\Gateway\Definition\UnitGatewayInterface;

/**
 * @internal
 */
final class MeasurementValueStorageGateway implements MeasurementValueStorageGatewayInterface
{
    private const VALUE_TABLE = 'ibexa_measurement_value';
    private const RANGE_VALUE_TABLE = 'ibexa_measurement_range_value';

    private Connection $connection;

    private UnitGatewayInterface $unitGateway;

    public function __construct(
        Connection $connection,
        UnitGatewayInterface $unitGateway
    ) {
        $this->connection = $connection;
        $this->unitGateway = $unitGateway;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function storeSimpleValue(
        int $fieldId,
        int $versionNo,
        string $measurementType,
        string $measurementUnit,
        float $value
    ): void {
        $this->deleteFieldDataFromTable(self::VALUE_TABLE, [$fieldId], $versionNo);
        $unitId = $this->unitGateway->getUnitId($measurementType, $measurementUnit);

        $this->connection->insert(
            self::VALUE_TABLE,
            [
                'content_field_id' => $fieldId,
                'version_no' => $versionNo,
                'unit_id' => $unitId,
                'value' => $value,
            ],
            [
                'content_field_id' => ParameterType::INTEGER,
                'version_no' => ParameterType::INTEGER,
                'unit_id' => ParameterType::INTEGER,
                'value' => ParameterType::STRING,
            ]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function storeRangeValue(
        int $fieldId,
        int $versionNo,
        string $measurementType,
        string $measurementUnit,
        float $minValue,
        float $maxValue
    ): void {
        $this->deleteFieldDataFromTable(self::RANGE_VALUE_TABLE, [$fieldId], $versionNo);
        $unitId = $this->unitGateway->getUnitId($measurementType, $measurementUnit);

        $this->connection->insert(
            self::RANGE_VALUE_TABLE,
            [
                'content_field_id' => $fieldId,
                'version_no' => $versionNo,
                'unit_id' => $unitId,
                'min_value' => $minValue,
                'max_value' => $maxValue,
            ],
            [
                'content_field_id' => ParameterType::INTEGER,
                'version_no' => ParameterType::INTEGER,
                'unit_id' => ParameterType::INTEGER,
                'min_value' => ParameterType::STRING,
                'max_value' => ParameterType::STRING,
            ]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadSimpleValueFieldData(int $fieldId, int $versionNo): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('t.name AS type_name', 'u.identifier AS unit_identifier', 'v.value')
            ->from(self::VALUE_TABLE, 'v')
            ->join('v', UnitGateway::UNIT_TABLE, 'u', 'v.unit_id = u.id')
            ->join('u', TypeGateway::TYPE_TABLE, 't', 'u.type_id = t.id')
            ->where('content_field_id = :content_field_id')
            ->andWhere('version_no = :version_no')
            ->setParameter('content_field_id', $fieldId, ParameterType::INTEGER)
            ->setParameter('version_no', $versionNo, ParameterType::INTEGER);

        $row = $queryBuilder->execute()->fetchAssociative();

        return false !== $row ? $row : [];
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function loadRangeValueFieldData(int $fieldId, int $versionNo): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select(
                't.name AS type_name',
                'u.identifier as unit_identifier',
                'rv.min_value',
                'rv.max_value'
            )
            ->from(self::RANGE_VALUE_TABLE, 'rv')
            ->join('rv', UnitGateway::UNIT_TABLE, 'u', 'rv.unit_id = u.id')
            ->join('u', TypeGateway::TYPE_TABLE, 't', 'u.type_id = t.id')
            ->where('content_field_id = :content_field_id')
            ->andWhere('version_no = :version_no')
            ->setParameter('content_field_id', $fieldId, ParameterType::INTEGER)
            ->setParameter('version_no', $versionNo, ParameterType::INTEGER);

        $row = $queryBuilder->execute()->fetchAssociative();

        return false !== $row ? $row : [];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteFieldData(array $fieldIds, int $versionNo): void
    {
        $this->deleteFieldDataFromTable(self::VALUE_TABLE, $fieldIds, $versionNo);
        $this->deleteFieldDataFromTable(self::RANGE_VALUE_TABLE, $fieldIds, $versionNo);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     *
     * @param int[] $fieldIds
     */
    private function deleteFieldDataFromTable(string $table, array $fieldIds, int $versionNo): void
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->delete($table)
            ->where('content_field_id IN (:content_field_ids)')
            ->andWhere('version_no = :version_no')
            ->setParameter('content_field_ids', $fieldIds, Connection::PARAM_INT_ARRAY)
            ->setParameter('version_no', $versionNo, ParameterType::INTEGER);

        $queryBuilder->execute();
    }
}
