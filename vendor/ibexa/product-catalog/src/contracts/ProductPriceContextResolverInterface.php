<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog;

use Ibexa\Contracts\ProductCatalog\Values\Price\PriceContextInterface;

interface ProductPriceContextResolverInterface
{
    public function resolveContext(?PriceContextInterface $context = null): PriceContextInterface;
}
