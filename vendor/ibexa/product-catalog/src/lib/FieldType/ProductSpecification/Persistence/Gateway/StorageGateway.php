<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineRelationship;
use Ibexa\Contracts\CorePersistence\Gateway\DoctrineSchemaMetadata;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Product\Gateway\StorageSchema as ProductStorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Values\Product;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantUpdateStruct;

/**
 * @phpstan-type Data array{
 *     id: int,
 *     code: string,
 *     content_id: int,
 *     version_no: int,
 *     base_product_id: int,
 *     field_id: int,
 * }
 *
 * @extends \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<Data>
 *
 * @implements \Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageGatewayInterface<Data>
 */
final class StorageGateway extends AbstractDoctrineDatabase implements StorageGatewayInterface
{
    protected function getTableName(): string
    {
        return StorageSchema::TABLE_NAME;
    }

    protected function buildMetadata(): DoctrineSchemaMetadata
    {
        $identifierColumns = [StorageSchema::COLUMN_ID];
        $columnToTypesMap = [
            StorageSchema::COLUMN_ID => Types::INTEGER,
            StorageSchema::COLUMN_PRODUCT_ID => Types::INTEGER,
            StorageSchema::COLUMN_CODE => Types::STRING,
            StorageSchema::COLUMN_CONTENT_ID => Types::INTEGER,
            StorageSchema::COLUMN_VERSION_NO => Types::INTEGER,
            StorageSchema::COLUMN_FIELD_ID => Types::INTEGER,
            StorageSchema::COLUMN_BASE_PRODUCT_ID => Types::INTEGER,
        ];

        $metadata = new DoctrineSchemaMetadata(
            $this->connection,
            ProductInterface::class,
            $this->getTableName(),
            $columnToTypesMap,
            $identifierColumns,
        );

        $metadata->addRelationship(new DoctrineRelationship(
            Product::class,
            'product',
            StorageSchema::COLUMN_PRODUCT_ID,
            ProductStorageSchema::COLUMN_ID
        ));

        return $metadata;
    }

    /**
     * @return array<string, mixed>|null
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findByContentIdAndVersionNo(int $contentId, int $versionNo): ?array
    {
        return $this->findOneBy([
            StorageSchema::COLUMN_CONTENT_ID => $contentId,
            StorageSchema::COLUMN_VERSION_NO => $versionNo,
        ]);
    }

    public function findByCode(string $code): ?array
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select(...StorageSchema::getColumns())
            ->from(StorageSchema::TABLE_NAME)
            ->where(
                $query->expr()->eq(
                    StorageSchema::COLUMN_CODE,
                    $query->createNamedParameter($code)
                )
            )
            ->orderBy(StorageSchema::COLUMN_ID, 'DESC')
            ->setMaxResults(1);

        $data = $query->execute()->fetchAssociative();
        if ($data === false) {
            return null;
        }

        /** @phpstan-var Data */
        return $this->getMetadata()->convertToPHPValues($data);
    }

    public function findByFieldId(int $id): ?array
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select(...StorageSchema::getColumns())
            ->from(StorageSchema::TABLE_NAME)
            ->where(
                $query->expr()->eq(
                    StorageSchema::COLUMN_FIELD_ID,
                    $query->createNamedParameter($id)
                )
            )
            ->orderBy(StorageSchema::COLUMN_ID, 'DESC')
            ->setMaxResults(1);

        $data = $query->execute()->fetchAssociative();
        if ($data === false) {
            return null;
        }

        /** @phpstan-var Data */
        return $this->getMetadata()->convertToPHPValues($data);
    }

    /**
     * @param int[] $fieldIds
     */
    public function deleteByFieldIds(array $fieldIds, int $versionNo): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->delete($this->getTableName())
            ->where(
                $qb->expr()->and(
                    $qb->expr()->in(
                        StorageSchema::COLUMN_FIELD_ID,
                        $qb->createNamedParameter($fieldIds, Connection::PARAM_INT_ARRAY)
                    ),
                    $qb->expr()->eq(
                        StorageSchema::COLUMN_VERSION_NO,
                        $qb->createNamedParameter($versionNo, ParameterType::INTEGER)
                    )
                )
            );

        $qb->execute();
    }

    /**
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function exists(int $contentId, int $versionNo): bool
    {
        return $this->findByContentIdAndVersionNo($contentId, $versionNo) !== null;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function insert(int $productId, int $contentId, int $versionNo, int $fieldId, string $code): int
    {
        return $this->doInsert([
            StorageSchema::COLUMN_PRODUCT_ID => $productId,
            StorageSchema::COLUMN_CONTENT_ID => $contentId,
            StorageSchema::COLUMN_VERSION_NO => $versionNo,
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
            StorageSchema::COLUMN_CODE => $code,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function delete(int $contentId, int $versionNo, int $fieldId): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_CONTENT_ID => $contentId,
            StorageSchema::COLUMN_VERSION_NO => $versionNo,
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
        ]);
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function update(int $contentId, int $versionNo, int $fieldId, string $code): void
    {
        $criteria = [
            StorageSchema::COLUMN_CONTENT_ID => $contentId,
            StorageSchema::COLUMN_VERSION_NO => $versionNo,
            StorageSchema::COLUMN_FIELD_ID => $fieldId,
        ];
        $data = [
            StorageSchema::COLUMN_CODE => $code,
        ];

        $this->doUpdate($criteria, $data);
    }

    public function countVariants(int $productId): int
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select($this->connection->getDatabasePlatform()->getCountExpression(StorageSchema::COLUMN_ID))
            ->from(StorageSchema::TABLE_NAME)
            ->where(
                $query->expr()->eq(
                    StorageSchema::COLUMN_BASE_PRODUCT_ID,
                    $query->createNamedParameter($productId)
                )
            );

        return (int)$query->execute()->fetchOne();
    }

    public function findVariants(int $productId, int $offset, int $limit): iterable
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select(...StorageSchema::getColumns())
            ->from(StorageSchema::TABLE_NAME)
            ->where(
                $query->expr()->eq(
                    StorageSchema::COLUMN_BASE_PRODUCT_ID,
                    $query->createNamedParameter($productId)
                )
            )
            ->orderBy(StorageSchema::COLUMN_ID, 'ASC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $data = $query->execute()->fetchAllAssociative();

        /** @phpstan-var Data[] */
        return array_map([$this->getMetadata(), 'convertToPHPValues'], $data);
    }

    public function insertVariant(string $code, int $fieldId, int $productId): int
    {
        $this->doInsert([
            StorageSchema::COLUMN_PRODUCT_ID => $productId,
            StorageSchema::COLUMN_CODE => $code,
            StorageSchema::COLUMN_BASE_PRODUCT_ID => $fieldId,
        ]);

        return (int)$this->connection->lastInsertId();
    }

    public function deleteVariant(string $code): void
    {
        $this->doDelete([
            StorageSchema::COLUMN_CODE => $code,
        ]);
    }

    /**
     * @return string[]
     */
    public function deleteVariantsByBaseProductId(int $baseProductId): array
    {
        $query = $this->connection->createQueryBuilder();

        $query
            ->select(StorageSchema::COLUMN_CODE)
            ->from(StorageSchema::TABLE_NAME)
            ->where(
                $query->expr()->eq(
                    StorageSchema::COLUMN_BASE_PRODUCT_ID,
                    $query->createNamedParameter($baseProductId)
                )
            )
            ->orderBy(StorageSchema::COLUMN_ID, 'ASC');

        $variantCodes = $query->execute()->fetchFirstColumn();

        $this->doDelete([
            StorageSchema::COLUMN_BASE_PRODUCT_ID => $baseProductId,
        ]);

        return $variantCodes;
    }

    public function updateVariant(ProductVariantUpdateStruct $updateStruct): void
    {
        $this->doUpdate(
            [StorageSchema::COLUMN_ID => $updateStruct->id],
            [StorageSchema::COLUMN_CODE => $updateStruct->code]
        );
    }
}
