<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability;

use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;

interface GatewayInterface
{
    public function insert(ProductAvailabilityCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Core\Base\Exceptions\NotFoundException
     */
    public function update(ProductAvailabilityUpdateStruct $updateStruct): void;

    /**
     * @phpstan-return array{
     *   id: int,
     *   availability: bool,
     *   is_infinite: bool,
     *   stock: int|null,
     *   product_code: string,
     * }|null
     */
    public function find(string $productCode): ?array;

    /**
     * @phpstan-return array{
     *   id: int,
     *   availability: bool,
     *   is_infinite: bool,
     *   stock: int|null,
     *   product_code: string,
     * }|null
     */
    public function findAggregatedForBaseProduct(string $productCode, int $productSpecificationId): ?array;

    public function deleteByProductCode(string $productCode): void;

    public function deleteByBaseProductCodeWithVariants(string $baseProductCode, int $productSpecificationId): void;

    public function updateProductCode(string $newProductCode, string $oldProductCode): void;
}
