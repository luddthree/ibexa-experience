<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Product;

use Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway\StorageSchema;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct;
use Ibexa\ProductCatalog\Local\Persistence\Values\Product;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariant;

final class Mapper
{
    /**
     * @phpstan-param array{
     *     id: string|int,
     *     code: string,
     *     version_no: string|int|null,
     *     content_id: string|int|null,
     *     base_product_id: string|int|null,
     *     field_id: int|null,
     * } $row
     */
    public function createFromRow(array $row): AbstractProduct
    {
        if ($row['base_product_id'] !== null) {
            return $this->createVariantFromRow($row);
        }

        return $this->createProductFromRow($row);
    }

    /**
     * @phpstan-param array{
     *     id: string|int,
     *     code: string,
     *     version_no: string|int|null,
     *     content_id: string|int|null,
     *     base_product_id: string|int|null
     * } $row
     */
    public function createProductFromRow(array $row): Product
    {
        $entity = new Product();
        $entity->id = (int) $row[StorageSchema::COLUMN_ID];
        $entity->code = $row[StorageSchema::COLUMN_CODE];
        $entity->versionId = (int) $row[StorageSchema::COLUMN_VERSION_NO];
        $entity->contentId = (int) $row[StorageSchema::COLUMN_CONTENT_ID];

        return $entity;
    }

    /**
     * @phpstan-param array{
     *     id: string|int,
     *     code: string,
     *     version_no: string|int|null,
     *     content_id: string|int|null,
     *     base_product_id: string|int|null
     * } $row
     */
    public function createVariantFromRow(array $row): ProductVariant
    {
        $entity = new ProductVariant();
        $entity->id = (int) $row[StorageSchema::COLUMN_ID];
        $entity->code = $row[StorageSchema::COLUMN_CODE];
        $entity->baseProductId = (int) $row[StorageSchema::COLUMN_BASE_PRODUCT_ID];

        return $entity;
    }

    public function createVariantFromCreateStruct(int $id, string $code, int $baseProductId): ProductVariant
    {
        $entity = new ProductVariant();
        $entity->id = $id;
        $entity->code = $code;
        $entity->baseProductId = $baseProductId;

        return $entity;
    }

    /**
     * @phpstan-param array{
     *     id: string|int,
     *     code: string,
     *     version_no: string|int,
     *     content_id: string|int,
     *     base_product_id: int|string|null,
     *     field_id: int|null,
     * }[] $rows
     *
     * @return \Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct[]
     */
    public function createFromRows(array $rows): array
    {
        $entities = [];
        foreach ($rows as $row) {
            $entities[] = $this->createFromRow($row);
        }

        return $entities;
    }
}
