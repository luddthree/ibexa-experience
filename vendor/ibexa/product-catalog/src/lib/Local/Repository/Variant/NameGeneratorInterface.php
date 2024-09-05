<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Variant;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface NameGeneratorInterface
{
    public function generateName(ProductInterface $product): string;
}
