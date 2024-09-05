<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory\Gateway;

use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductTypeVatCategory\GatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategory;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductTypeVatCategoryCreateStruct;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *     id: int,
 *     field_definition_id: int,
 *     status: int,
 *     region: string,
 *     vat_category: string,
 * }>
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
            StorageSchema::COLUMN_REGION => Types::STRING,
            StorageSchema::COLUMN_VAT_CATEGORY => Types::STRING,
        ];
        $identifierColumns = [
            StorageSchema::COLUMN_ID,
        ];

        return new DoctrineSchemaMetadata(
            $this->connection,
            ProductTypeVatCategory::class,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns,
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteBy(array $criteria): void
    {
        $this->doDelete($criteria);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(ProductTypeVatCategoryCreateStruct $createStruct): int
    {
        $data = [
            StorageSchema::COLUMN_FIELD_DEFINITION_ID => $createStruct->getFieldDefinitionId(),
            StorageSchema::COLUMN_STATUS => $createStruct->getStatus(),
            StorageSchema::COLUMN_REGION => $createStruct->getRegion(),
            StorageSchema::COLUMN_VAT_CATEGORY => $createStruct->getVatCategory(),
        ];

        return $this->doInsert($data);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(int $id): void
    {
        $criteria = [
            StorageSchema::COLUMN_ID => $id,
        ];

        $this->doDelete($criteria);
    }
}
