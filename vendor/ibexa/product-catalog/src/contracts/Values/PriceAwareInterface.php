<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;

interface PriceAwareInterface
{
    public function getPrice(?PriceContextInterface $context = null): ?PriceInterface;
}
