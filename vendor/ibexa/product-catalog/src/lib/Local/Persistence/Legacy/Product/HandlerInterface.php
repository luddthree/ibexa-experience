<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\Product;

use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariant;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductVariantUpdateStruct;

/**
 * @internal
 */
interface HandlerInterface
{
    public function isCodeUnique(string $code): bool;

    public function findById(int $id): AbstractProduct;

    public function findByCode(string $code): AbstractProduct;

    public function countVariants(int $productId): int;

    /**
     * @return iterable<\Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProduct>
     */
    public function findVariants(int $productId, int $offset, int $limit): iterable;

    public function createVariant(ProductVariantCreateStruct $createStruct): ProductVariant;

    public function deleteVariant(string $code): void;

    /**
     * @return string[]
     */
    public function deleteVariantsByBaseProductId(int $baseProductId): array;

    public function updateVariant(ProductVariantUpdateStruct $updateStruct): void;
}
