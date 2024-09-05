<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Storage\Gateway\Definition;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;

/**
 * @internal
 */
final class UnitGateway implements UnitGatewayInterface
{
    public const UNIT_TABLE = 'ibexa_measurement_unit';

    private Connection $connection;

    private TypeGatewayInterface $typeGateway;

    public function __construct(
        Connection $connection,
        TypeGatewayInterface $typeGateway
    ) {
        $this->connection = $connection;
        $this->typeGateway = $typeGateway;
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUnitId(string $typeName, string $unitIdentifier): int
    {
        $typeId = $this->typeGateway->getTypeId($typeName);
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from(self::UNIT_TABLE)
            ->where('type_id = :type_id')
            ->andWhere('identifier = :identifier')
            ->setParameter('type_id', $typeId, ParameterType::INTEGER)
            ->setParameter('identifier', $unitIdentifier, ParameterType::STRING);

        $unitId = $queryBuilder->execute()->fetchOne();

        if ($unitId === false) {
            return $this->createUnit($typeId, $unitIdentifier);
        }

        return (int)$unitId;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function createUnit(int $typeId, string $unitIdentifier): int
    {
        $this->connection->insert(
            self::UNIT_TABLE,
            [
                'type_id' => $typeId,
                'identifier' => $unitIdentifier,
            ],
            [
                'type_id' => ParameterType::INTEGER,
            ]
        );

        return (int)$this->connection->lastInsertId();
    }

    public function getUnitIdentifier(int $unitId): string
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('identifier')
            ->from(self::UNIT_TABLE)
            ->andWhere(
                $queryBuilder->expr()->eq(
                    'id',
                    $queryBuilder->createNamedParameter($unitId, ParameterType::INTEGER)
                ),
            )
            ->setMaxResults(1);

        $typeName = $queryBuilder->execute()->fetchOne();

        if ($typeName === false) {
            throw new NotFoundException(UnitInterface::class, $unitId);
        }

        return (string)$typeName;
    }
}
