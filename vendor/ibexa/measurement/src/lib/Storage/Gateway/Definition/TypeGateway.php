<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Storage\Gateway\Definition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;

/**
 * @internal
 */
final class TypeGateway implements TypeGatewayInterface
{
    public const TYPE_TABLE = 'ibexa_measurement_type';

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getTypeId(string $measurementType): int
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from(self::TYPE_TABLE)
            ->where('name = :name')
            ->setParameter('name', $measurementType, ParameterType::STRING)
            ->setMaxResults(1);

        $typeId = $queryBuilder->execute()->fetchOne();

        if ($typeId === false) {
            return $this->createType($measurementType);
        }

        return (int)$typeId;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function createType(string $measurementType): int
    {
        $this->connection->insert(
            self::TYPE_TABLE,
            [
                'name' => $measurementType,
            ],
        );

        return (int)$this->connection->lastInsertId();
    }

    public function getTypeNameByUnitId(int $unitId): string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('type.name')
            ->from(self::TYPE_TABLE, 'type')
            ->join(
                'type',
                UnitGateway::UNIT_TABLE,
                'unit',
                $queryBuilder->expr()->eq(
                    'unit.type_id',
                    'type.id',
                )
            )
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'unit.id',
                    $queryBuilder->createNamedParameter($unitId, ParameterType::INTEGER),
                ),
            )
            ->setMaxResults(1);

        $typeName = $queryBuilder->execute()->fetchOne();

        if ($typeName === false) {
            throw new NotFoundException(MeasurementInterface::class, $unitId);
        }

        return (string)$typeName;
    }
}
