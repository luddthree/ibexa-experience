<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\FieldType\ProductSpecification\Persistence\Gateway;

use Ibexa\Contracts\Core\FieldType\StorageGatewayInterface as CoreFieldStorageGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\GatewayInterface as BaseGatewayInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantUpdateStruct;

/**
 * @internal
 *
 * @template T of array
 *
 * @extends  \Ibexa\ProductCatalog\Local\Persistence\Legacy\GatewayInterface<T>
 */
interface StorageGatewayInterface extends CoreFieldStorageGatewayInterface, BaseGatewayInterface
{
    /**
     * @return T|null
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function findByContentIdAndVersionNo(int $contentId, int $versionNo): ?array;

    /**
     * @phpstan-return T|null
     */
    public function findByCode(string $code): ?array;

    /**
     * @phpstan-return T|null
     */
    public function findByFieldId(int $id): ?array;

    /**
     * @param int[] $fieldIds
     */
    public function deleteByFieldIds(array $fieldIds, int $versionNo): void;

    public function exists(int $contentId, int $versionNo): bool;

    public function insert(int $productId, int $contentId, int $versionNo, int $fieldId, string $code): int;

    public function delete(int $contentId, int $versionNo, int $fieldId): void;

    public function update(int $contentId, int $versionNo, int $fieldId, string $code): void;

    public function insertVariant(string $code, int $fieldId, int $productId): int;

    /**
     * @return iterable<T>
     */
    public function findVariants(int $productId, int $offset, int $limit): iterable;

    public function countVariants(int $productId): int;

    public function deleteVariant(string $code): void;

    /**
     * @return string[]
     */
    public function deleteVariantsByBaseProductId(int $baseProductId): array;

    public function updateVariant(ProductVariantUpdateStruct $updateStruct): void;
}
