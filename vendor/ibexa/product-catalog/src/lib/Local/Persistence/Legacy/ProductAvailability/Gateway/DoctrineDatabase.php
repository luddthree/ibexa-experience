<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema as ProductSpecificationStorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability\GatewayInterface;

/**
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<array{
 *     id: int,
 *     availability: bool|null,
 *     product_code: string,
 *     stock: int|null,
 * }>
 */
final class DoctrineDatabase extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase implements GatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function getTableAlias(): string
    {
        return 'ipsa';
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_AVAILABILITY => Types::BOOLEAN,
            StorageSchema::COLUMN_PRODUCT_CODE => Types::STRING,
            StorageSchema::COLUMN_STOCK => Types::INTEGER,
            StorageSchema::COLUMN_IS_INFINITE => Types::BOOLEAN,
        ];
        $identifierColumns = [
            StorageSchema::COLUMN_ID,
        ];

        return new DoctrineSchemaMetadata(
            $this->connection,
            AvailabilityInterface::class,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns
        );
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(ProductAvailabilityCreateStruct $createStruct): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_PRODUCT_CODE => $createStruct->getProduct()->getCode(),
            StorageSchema::COLUMN_AVAILABILITY => $createStruct->getAvailability(),
            StorageSchema::COLUMN_STOCK => $createStruct->getStock(),
            StorageSchema::COLUMN_IS_INFINITE => $createStruct->isInfinite(),
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(ProductAvailabilityUpdateStruct $updateStruct): void
    {
        $data = [];
        if ($updateStruct->getAvailability() !== null) {
            $data[StorageSchema::COLUMN_AVAILABILITY] = $updateStruct->getAvailability();
        }
        if ($updateStruct->isInfinite() !== null) {
            $data[StorageSchema::COLUMN_IS_INFINITE] = $updateStruct->isInfinite();
        }
        if ($updateStruct->getStock() !== null || $updateStruct->isInfinite() === true) {
            $data[StorageSchema::COLUMN_STOCK] = $updateStruct->getStock();
        }

        $criteria = [
            StorageSchema::COLUMN_PRODUCT_CODE => $updateStruct->getProduct()->getCode(),
        ];
        if (!empty($data)) {
            $this->doUpdate($criteria, $data);
        }
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function find(string $productCode): ?array
    {
        /**
         * @var array{
         *     id: int,
         *     availability: bool,
         *     is_infinite: bool,
         *     stock: int|null,
         *     product_code: string,
         * }|null
         */
        return $this->findOneBy([
            StorageSchema::COLUMN_PRODUCT_CODE => $productCode,
        ]);
    }

    public function findAggregatedForBaseProduct(string $productCode, int $productSpecificationId): ?array
    {
        $query = $this->connection->createQueryBuilder();
        $subquery = $this->connection->createQueryBuilder();

        $columns = [
            $this->getBooleanSumExpression(StorageSchema::COLUMN_AVAILABILITY),
            $this->getSumExpression(StorageSchema::COLUMN_STOCK),
            $this->getBooleanSumExpression(StorageSchema::COLUMN_IS_INFINITE),
        ];

        $subquery
            ->select(ProductSpecificationStorageSchema::COLUMN_CODE)
            ->from(ProductSpecificationStorageSchema::TABLE_NAME)
            ->andWhere(
                $query->expr()->eq(
                    ProductSpecificationStorageSchema::COLUMN_BASE_PRODUCT_ID,
                    $query->createNamedParameter($productSpecificationId)
                )
            );

        $query
            ->select(...$columns)
            ->from(StorageSchema::TABLE_NAME)
            ->andWhere(
                $query->expr()->in(
                    StorageSchema::COLUMN_PRODUCT_CODE,
                    $subquery->getSQL()
                )
            );

        $result = $query->execute()->fetchAssociative();

        if ($result === false) {
            return null;
        }

        $availability = (int)$result[StorageSchema::COLUMN_AVAILABILITY] > 0;
        $isInfiniteStock = (int)$result[StorageSchema::COLUMN_IS_INFINITE] > 0;
        $stock = $result[StorageSchema::COLUMN_STOCK] === null || $isInfiniteStock ?
            null :
            (int)$result[StorageSchema::COLUMN_STOCK];

        return [
            StorageSchema::COLUMN_ID => $productSpecificationId,
            StorageSchema::COLUMN_AVAILABILITY => $availability,
            StorageSchema::COLUMN_IS_INFINITE => $isInfiniteStock,
            StorageSchema::COLUMN_STOCK => $stock,
            StorageSchema::COLUMN_PRODUCT_CODE => $productCode,
        ];
    }

    public function deleteByProductCode(string $productCode): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_PRODUCT_CODE => $productCode,
        ]);
    }

    public function deleteByBaseProductCodeWithVariants(
        string $baseProductCode,
        int $productSpecificationId
    ): void {
        $query = $this->connection->createQueryBuilder();
        $subquery = $this->connection->createQueryBuilder();

        $subquery
            ->select(ProductSpecificationStorageSchema::COLUMN_CODE)
            ->from(ProductSpecificationStorageSchema::TABLE_NAME)
            ->andWhere(
                $query->expr()->eq(
                    ProductSpecificationStorageSchema::COLUMN_BASE_PRODUCT_ID,
                    $query->createNamedParameter($productSpecificationId)
                )
            );

        $query
            ->select(StorageSchema::COLUMN_PRODUCT_CODE)
            ->from(StorageSchema::TABLE_NAME)
            ->andWhere(
                $query->expr()->in(
                    StorageSchema::COLUMN_PRODUCT_CODE,
                    $subquery->getSQL()
                )
            );

        $result = $query->execute()->fetchAllAssociative();

        $codes = array_column($result, StorageSchema::COLUMN_PRODUCT_CODE);
        $codes[] = $baseProductCode;

        $deleteQuery = $this->connection->createQueryBuilder();
        $deleteQuery
            ->delete(StorageSchema::TABLE_NAME)
            ->andWhere(
                $deleteQuery->expr()->in(
                    StorageSchema::COLUMN_PRODUCT_CODE,
                    $deleteQuery->createNamedParameter($codes, Connection::PARAM_STR_ARRAY)
                )
            )
            ->execute();
    }

    private function getBooleanSumExpression(string $column): string
    {
        return $this
            ->connection
            ->getDatabasePlatform()
            ->getSumExpression('CASE WHEN ' . $column . ' THEN 1 ELSE 0 END') . ' AS ' . $column;
    }

    private function getSumExpression(string $column): string
    {
        return $this
            ->connection
            ->getDatabasePlatform()
            ->getSumExpression($column) . ' AS ' . $column;
    }

    public function updateProductCode(string $newProductCode, string $oldProductCode): void
    {
        $data[StorageSchema::COLUMN_PRODUCT_CODE] = $newProductCode;

        $criteria = [
            StorageSchema::COLUMN_PRODUCT_CODE => $oldProductCode,
        ];

        $this->doUpdate($criteria, $data);
    }
}
