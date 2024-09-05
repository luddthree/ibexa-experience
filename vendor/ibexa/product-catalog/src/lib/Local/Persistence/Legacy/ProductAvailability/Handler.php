<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductAvailability;

use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductAvailability;

final class Handler implements HandlerInterface
{
    private GatewayInterface $gateway;

    private Mapper $mapper;

    public function __construct(GatewayInterface $gateway, Mapper $mapper)
    {
        $this->gateway = $gateway;
        $this->mapper = $mapper;
    }

    public function create(ProductAvailabilityCreateStruct $createStruct): int
    {
        return $this->gateway->insert($createStruct);
    }

    public function update(ProductAvailabilityUpdateStruct $updateStruct): void
    {
        $this->gateway->update($updateStruct);
    }

    public function find(string $productCode): ProductAvailability
    {
        $row = $this->gateway->find($productCode);

        if ($row === null) {
            throw new NotFoundException(ProductAvailability::class, $productCode);
        }

        return $this->mapper->createFromRow($row);
    }

    public function exists(string $productCode): bool
    {
        $row = $this->gateway->find($productCode);

        return $row !== null;
    }

    public function findAggregatedForBaseProduct(string $productCode, int $productSpecificationId): ProductAvailability
    {
        $row = $this->gateway->findAggregatedForBaseProduct($productCode, $productSpecificationId);

        if ($row === null) {
            throw new NotFoundException(ProductAvailability::class, $productCode);
        }

        return $this->mapper->createFromRow($row);
    }

    public function deleteByProductCode(string $productCode): void
    {
        $this->gateway->deleteByProductCode($productCode);
    }

    public function deleteByBaseProductCodeWithVariants(string $baseProductCode, int $productSpecificationId): void
    {
        $this->gateway->deleteByBaseProductCodeWithVariants($baseProductCode, $productSpecificationId);
    }

    public function updateProductCode(string $newProductCode, string $oldProductCode): void
    {
        $this->gateway->updateProductCode($newProductCode, $oldProductCode);
    }
}
