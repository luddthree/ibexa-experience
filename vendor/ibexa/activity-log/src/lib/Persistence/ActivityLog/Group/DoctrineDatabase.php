<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog\Group;

use DateTimeImmutable;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ibexa\ActivityLog\Persistence\ActivityLog\Action\Action;
use Ibexa\ActivityLog\Persistence\ActivityLog\Ip\Ip;
use Ibexa\ActivityLog\Persistence\ActivityLog\Ip\StorageSchema as IpStorageSchema;
use Ibexa\ActivityLog\Persistence\ActivityLog\LogEntryInterface;
use Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectClass;
use Ibexa\ActivityLog\Persistence\ActivityLog\StorageSchema as LogEntryStorageSchema;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineRelationship;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadataInterface;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\Group\GatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadataInterface
    {
        $columns = [
            StorageSchema::COLUMN_ID => Types::BIGINT,
            StorageSchema::COLUMN_DESCRIPTION => Types::STRING,
            StorageSchema::COLUMN_SOURCE_ID => Types::INTEGER,
            StorageSchema::COLUMN_IP_ID => Types::INTEGER,
            StorageSchema::COLUMN_LOGGED_AT => Types::DATETIME_IMMUTABLE,
            StorageSchema::COLUMN_USER_ID => Types::INTEGER,
        ];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            Group::class,
            $this->getTableName(),
            $columns,
            [StorageSchema::COLUMN_ID],
        );

        $relationship = new DoctrineRelationship(
            LogEntryInterface::class,
            'log_record',
            LogEntryStorageSchema::COLUMN_GROUP_ID,
            StorageSchema::COLUMN_ID,
            DoctrineRelationship::JOIN_TYPE_JOINED,
        );
        $metadata->addRelationship($relationship);

        $relationship = new DoctrineRelationship(
            Ip::class,
            'ip',
            IpStorageSchema::COLUMN_IP,
            StorageSchema::COLUMN_IP_ID,
            DoctrineRelationship::JOIN_TYPE_JOINED,
        );
        $metadata->addRelationship($relationship);

        return $metadata;
    }

    public function findBy($criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $metadata = $this->getMetadata();
        $qb = $this->createBaseQueryBuilder();
        $qb->distinct();

        $this->applyJoins($qb);
        $this->applyOrderBy($qb, $orderBy);
        $this->applyCriteria($qb, $criteria);
        $this->applyLimits($qb, $limit, $offset);

        $results = $qb->execute()->fetchAllAssociative();

        /** @phpstan-var array<Data> */
        return array_map([$metadata, 'convertToPHPValues'], $results);
    }

    public function countBy($criteria): int
    {
        $metadata = $this->getMetadata();
        $qb = $this->createBaseQueryBuilder();
        $platform = $this->connection->getDatabasePlatform();
        $qb->select(
            $platform->getCountExpression(
                sprintf('DISTINCT %s.%s', $this->getTableAlias(), $metadata->getIdentifierColumn())
            ),
        );

        $this->applyJoins($qb);
        $this->applyCriteria($qb, $criteria);

        $result = $qb->execute()->fetchFirstColumn();

        return (int)$result[0];
    }

    public function save(
        ?int $userId,
        ?int $sourceId,
        ?int $ipId,
        ?string $description
    ): int {
        return $this->doInsert([
            StorageSchema::COLUMN_SOURCE_ID => $sourceId,
            StorageSchema::COLUMN_DESCRIPTION => $description,
            StorageSchema::COLUMN_USER_ID => $userId,
            StorageSchema::COLUMN_IP_ID => $ipId,
            StorageSchema::COLUMN_LOGGED_AT => new DateTimeImmutable(),
        ]);
    }

    public function deleteBy(array $criteria): void
    {
        $qb = parent::createBaseQueryBuilder();

        $qb->delete($this->getTableName(), $this->getTableAlias());
        $this->applyJoins($qb);
        $this->applyCriteria($qb, $criteria);

        $qb->execute();
    }

    private function applyJoins(QueryBuilder $qb): void
    {
        $logMetadata = $this->applyLogEntryJoin($qb);

        $this->applyLogActionJoin($logMetadata, $qb);
        $this->applyLogObjectJoin($logMetadata, $qb);
    }

    private function applyLogEntryJoin(QueryBuilder $qb): DoctrineSchemaMetadataInterface
    {
        $logMetadata = $this->registry->getMetadata(LogEntryInterface::class);
        $logTableName = $logMetadata->getTableName();
        $logRelationship = $logMetadata->getRelationshipByForeignKeyColumn(LogEntryStorageSchema::COLUMN_GROUP_ID);
        $condition = sprintf(
            '%s.%s = %s.%s',
            $this->getTableAlias(),
            $logRelationship->getRelatedClassIdColumn(),
            $logTableName,
            $logRelationship->getForeignKeyColumn(),
        );

        $qb->join(
            $this->getTableAlias(),
            $logTableName,
            $logTableName,
            $condition,
        );

        return $logMetadata;
    }

    private function applyLogActionJoin(DoctrineSchemaMetadataInterface $logMetadata, QueryBuilder $qb): void
    {
        $actionMetadata = $this->registry->getMetadata(Action::class);
        $actionTableName = $actionMetadata->getTableName();
        $actionRelationship = $logMetadata->getRelationshipByForeignKeyColumn(LogEntryStorageSchema::COLUMN_ACTION_ID);
        $logTableName = $logMetadata->getTableName();
        $qb->join(
            $logTableName,
            $actionTableName,
            $actionTableName,
            sprintf(
                '%s.%s = %s.%s',
                $logTableName,
                $actionRelationship->getForeignKeyColumn(),
                $actionTableName,
                $actionRelationship->getRelatedClassIdColumn(),
            )
        );
    }

    private function applyLogObjectJoin(DoctrineSchemaMetadataInterface $logMetadata, QueryBuilder $qb): void
    {
        $objectMetadata = $this->registry->getMetadata(ObjectClass::class);
        $objectTableName = $objectMetadata->getTableName();
        $objectRelationship = $logMetadata->getRelationshipByForeignKeyColumn(LogEntryStorageSchema::COLUMN_OBJECT_CLASS_ID);
        $logTableName = $logMetadata->getTableName();
        $qb->join(
            $logTableName,
            $objectTableName,
            $objectTableName,
            sprintf(
                '%s.%s = %s.%s',
                $logTableName,
                $objectRelationship->getForeignKeyColumn(),
                $objectTableName,
                $objectRelationship->getRelatedClassIdColumn(),
            )
        );
    }
}
