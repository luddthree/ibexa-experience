<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityContextInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\AvailabilityInterface;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Availability\ProductAvailabilityUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface ProductAvailabilityServiceInterface
{
    public function getAvailability(
        ProductInterface $product,
        ?AvailabilityContextInterface $availabilityContext = null
    ): AvailabilityInterface;

    public function hasAvailability(
        ProductInterface $product
    ): bool;

    public function createProductAvailability(
        ProductAvailabilityCreateStruct $struct
    ): AvailabilityInterface;

    public function updateProductAvailability(
        ProductAvailabilityUpdateStruct $struct
    ): AvailabilityInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function increaseProductAvailability(
        ProductInterface $product,
        int $amount = 1
    ): AvailabilityInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function decreaseProductAvailability(
        ProductInterface $product,
        int $amount = 1
    ): AvailabilityInterface;

    public function deleteProductAvailability(
        ProductInterface $product
    ): void;
}
