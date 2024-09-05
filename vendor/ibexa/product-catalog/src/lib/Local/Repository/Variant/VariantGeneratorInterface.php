<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Variant;

use Ibexa\Contracts\ProductCatalog\Local\Values\Product\ProductVariantGenerateStruct;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;

interface VariantGeneratorInterface
{
    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function generateVariants(
        ProductInterface $product,
        ProductVariantGenerateStruct $variantGenerateStruct
    ): void;
}
