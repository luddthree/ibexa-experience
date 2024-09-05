<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Variant;

use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\ValueFormatterDispatcherInterface;

final class NameGenerator implements NameGeneratorInterface
{
    private ValueFormatterDispatcherInterface $valueFormatterDispatcher;

    public function __construct(ValueFormatterDispatcherInterface $valueFormatterDispatcher)
    {
        $this->valueFormatterDispatcher = $valueFormatterDispatcher;
    }

    public function generateName(ProductInterface $product): string
    {
        $name = '';
        if ($product instanceof ProductVariantInterface) {
            $name = $product->getBaseProduct()->getName();

            $chunks = [];
            foreach ($product->getDiscriminatorAttributes() as $attribute) {
                $chunks[] = $this->valueFormatterDispatcher->formatValue($attribute);
            }

            $name .= ' ' . implode('/', $chunks);
        }

        return $name;
    }
}
