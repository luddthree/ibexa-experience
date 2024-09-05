<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data\Availability;

use Ibexa\Contracts\ProductCatalog\Values\AvailabilityAwareInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class ProductAvailabilityIncreaseData extends AbstractProductAvailabilityData
{
    public function __construct(ProductInterface $product)
    {
        parent::__construct($product);

        if (!$product instanceof AvailabilityAwareInterface) {
            throw new InvalidArgumentException('product', 'must be an instance of ' . AvailabilityAwareInterface::class);
        }

        $availability = $product->getAvailability();
        $this->stock = $availability->getStock();
        $this->available = $availability->isAvailable();
    }
}
