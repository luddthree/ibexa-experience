<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\CustomerGroup\Persistence\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Core\FieldType\StorageGatewayInterface;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Type;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *     id: int,
 *     field_id: int,
 *     field_version_no: int,
 *     content_id: int,
 *     customer_group_id: int,
 * }>
 */
final class StorageGateway extends AbstractDoctrineDatabase implements StorageGatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_FIELD_ID => Types::INTEGER,
            StorageSchema::COLUMN_FIELD_VERSION_NO => Types::INTEGER,
            StorageSchema::COLUMN_CONTENT_ID => Types::INTEGER,
            StorageSchema::COLUMN_CUSTOMER_GROUP_ID => Types::INTEGER,
        ];
        $identifierColumns = [
            StorageSchema::COLUMN_ID,
        ];

        return new DoctrineSchemaMetadata(
            $this->connection,
            null,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns,
        );
    }

    /**
     * @phpstan-return array{
     *     customer_group_id: int,
     * }|null
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findByFieldIdAndVersionNo(int $fieldId, int $fieldVersionNo): ?array
    {
        $result = $this->findOneBy([
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
            StorageSchema::COLUMN_FIELD_VERSION_NO => $fieldVersionNo,
        ]);

        if (empty($result)) {
            return null;
        }

        return [
            Type::FIELD_ID_KEY => $result[StorageSchema::COLUMN_CUSTOMER_GROUP_ID],
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function exists(int $fieldId, int $fieldVersionNo): bool
    {
        return $this->findByFieldIdAndVersionNo($fieldId, $fieldVersionNo) !== null;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(int $fieldId, int $fieldVersionNo, int $contentId, int $customerGroupId): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
            StorageSchema::COLUMN_FIELD_VERSION_NO => $fieldVersionNo,
            StorageSchema::COLUMN_CONTENT_ID => $contentId,
            StorageSchema::COLUMN_CUSTOMER_GROUP_ID => $customerGroupId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(int $fieldId, int $fieldVersionNo, int $customerGroupId): void
    {
        $criteria = [
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
            StorageSchema::COLUMN_FIELD_VERSION_NO => $fieldVersionNo,
        ];
        $data = [
            StorageSchema::COLUMN_CUSTOMER_GROUP_ID => $customerGroupId,
        ];

        $this->doUpdate($criteria, $data);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(int $fieldId, int $versionNo): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
            StorageSchema::COLUMN_FIELD_VERSION_NO => $versionNo,
        ]);
    }
}
