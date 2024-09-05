<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability;

use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductAvailability;

interface HandlerInterface
{
    public function create(ProductAvailabilityCreateStruct $createStruct): int;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function update(ProductAvailabilityUpdateStruct $updateStruct): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function find(string $productCode): ProductAvailability;

    public function exists(string $productCode): bool;

    public function findAggregatedForBaseProduct(string $productCode, int $productSpecificationId): ProductAvailability;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function deleteByProductCode(string $productCode): void;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    public function deleteByBaseProductCodeWithVariants(string $baseProductCode, int $productSpecificationId): void;

    public function updateProductCode(string $newProductCode, string $oldProductCode): void;
}
