<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\Persistence\ActivityLog;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use Ibexa\ActivityLog\Persistence\ActivityLog\Action\Action;
use Ibexa\ActivityLog\Persistence\ActivityLog\Action\StorageSchema as ActionStorageSchema;
use Ibexa\ActivityLog\Persistence\ActivityLog\Group\Group;
use Ibexa\ActivityLog\Persistence\ActivityLog\Group\StorageSchema as GroupStorageSchema;
use Ibexa\ActivityLog\Persistence\ActivityLog\Object\ObjectClass;
use Ibexa\ActivityLog\Persistence\ActivityLog\Object\StorageSchema as ObjectClassStorageSchema;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineRelationship;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;

/**
 * @phpstan-import-type Data from \Ibexa\ActivityLog\Persistence\ActivityLog\GatewayInterface
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 */
final class DoctrineDatabase extends AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columns = [
            StorageSchema::COLUMN_ID => Types::BIGINT,
            StorageSchema::COLUMN_GROUP_ID => Types::BIGINT,
            StorageSchema::COLUMN_OBJECT_CLASS_ID => Types::INTEGER,
            StorageSchema::COLUMN_ACTION_ID => Types::INTEGER,
            StorageSchema::COLUMN_OBJECT_ID => Types::STRING,
            StorageSchema::COLUMN_OBJECT_NAME => Types::STRING,
            StorageSchema::COLUMN_DATA => Types::JSON,
        ];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            LogEntryInterface::class,
            $this->getTableName(),
            $columns,
            [
                StorageSchema::COLUMN_ID,
            ],
        );

        $metadata->addRelationship(new DoctrineRelationship(
            Group::class,
            'group',
            StorageSchema::COLUMN_GROUP_ID,
            GroupStorageSchema::COLUMN_ID,
            DoctrineRelationship::JOIN_TYPE_SUB_SELECT,
        ));

        $metadata->addRelationship(new DoctrineRelationship(
            Action::class,
            'action',
            StorageSchema::COLUMN_ACTION_ID,
            ActionStorageSchema::COLUMN_ID,
            DoctrineRelationship::JOIN_TYPE_JOINED,
        ));

        $metadata->addRelationship(new DoctrineRelationship(
            ObjectClass::class,
            'object_class',
            StorageSchema::COLUMN_OBJECT_CLASS_ID,
            ObjectClassStorageSchema::COLUMN_ID,
            DoctrineRelationship::JOIN_TYPE_JOINED,
        ));

        return $metadata;
    }

    protected function createBaseQueryBuilder(?string $tableAlias = null): QueryBuilder
    {
        $qb = parent::createBaseQueryBuilder($tableAlias);
        $metadata = $this->getMetadata();

        $actionMetadata = $this->registry->getMetadata(Action::class);
        $actionTableName = $actionMetadata->getTableName();
        $actionRelationship = $metadata->getRelationshipByForeignKeyColumn(StorageSchema::COLUMN_ACTION_ID);
        $qb->join(
            $this->getTableAlias(),
            $actionTableName,
            $actionTableName,
            sprintf(
                '%s.%s = %s.%s',
                $this->getTableAlias(),
                $actionRelationship->getForeignKeyColumn(),
                $actionMetadata->getTableName(),
                $actionRelationship->getRelatedClassIdColumn(),
            )
        );

        $objectMetadata = $this->registry->getMetadata(ObjectClass::class);
        $objectTableName = $objectMetadata->getTableName();
        $objectRelationship = $metadata->getRelationshipByForeignKeyColumn(StorageSchema::COLUMN_OBJECT_CLASS_ID);
        $qb->join(
            $this->getTableAlias(),
            $objectTableName,
            $objectTableName,
            sprintf(
                '%s.%s = %s.%s',
                $this->getTableAlias(),
                $objectRelationship->getForeignKeyColumn(),
                $objectMetadata->getTableName(),
                $objectRelationship->getRelatedClassIdColumn(),
            )
        );

        return $qb;
    }

    public function countBy($criteria): int
    {
        $metadata = $this->getMetadata();
        $qb = $this->createBaseQueryBuilder();

        $identifierColumn = $metadata->getIdentifierColumn();
        $tableAlias = $this->getTableAlias();
        $platform = $this->connection->getDatabasePlatform();
        $qb->select($platform->getCountExpression($tableAlias . '.' . $identifierColumn));

        $expr = $this->convertCriteriaToExpression($qb, $criteria);
        if ($expr !== null) {
            $qb->andWhere($expr);
        }

        return (int)$qb->execute()->fetchOne();
    }

    public function save(
        int $groupId,
        int $objectClassId,
        int $actionId,
        string $objectId,
        ?string $objectName,
        array $data
    ): int {
        return $this->doInsert([
            StorageSchema::COLUMN_GROUP_ID => $groupId,
            StorageSchema::COLUMN_OBJECT_CLASS_ID => $objectClassId,
            StorageSchema::COLUMN_ACTION_ID => $actionId,
            StorageSchema::COLUMN_OBJECT_ID => $objectId,
            StorageSchema::COLUMN_DATA => $data,
            StorageSchema::COLUMN_OBJECT_NAME => $objectName,
        ]);
    }
}
