<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\ProductVariant\Routing;

use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

/**
 * @internal used only for generating URL for product variant to be fetched by recommendation engine
 */
interface UrlGeneratorInterface
{
    public function generate(
        ProductVariantInterface $productVariant,
        ?string $lang = null
    ): string;

    /**
     * @param string[] $variantCodes
     */
    public function generateForVariantCodes(
        array $variantCodes,
        string $lang
    ): string;
}
