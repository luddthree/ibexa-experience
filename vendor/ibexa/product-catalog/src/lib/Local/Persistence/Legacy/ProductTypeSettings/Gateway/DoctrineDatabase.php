<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSetting;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeSettingCreateStruct;

/**
 * @internal
 *
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeSettings\GatewayInterface
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
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_FIELD_DEFINITION_ID => Types::INTEGER,
            StorageSchema::COLUMN_STATUS => Types::INTEGER,
            StorageSchema::COLUMN_IS_VIRTUAL => Types::BOOLEAN,
        ];
        $identifierColumns = [
            StorageSchema::COLUMN_ID,
        ];

        return new DoctrineSchemaMetadata(
            $this->connection,
            ProductTypeSetting::class,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(ProductTypeSettingCreateStruct $createStruct): int
    {
        return $this->doInsert(
            [
                StorageSchema::COLUMN_FIELD_DEFINITION_ID => $createStruct->getFieldDefinitionId(),
                StorageSchema::COLUMN_STATUS => $createStruct->getStatus(),
                StorageSchema::COLUMN_IS_VIRTUAL => $createStruct->isVirtual(),
            ]
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(array $data, int $fieldDefinitionId, int $status): void
    {
        $this->doUpdate(
            [
                StorageSchema::COLUMN_FIELD_DEFINITION_ID => $fieldDefinitionId,
                StorageSchema::COLUMN_STATUS => $status,
            ],
            $data
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteBy(array $criteria): void
    {
        $this->doDelete($criteria);
    }
}
