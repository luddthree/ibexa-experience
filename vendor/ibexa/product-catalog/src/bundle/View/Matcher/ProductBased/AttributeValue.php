<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\View\Matcher\ProductBased;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\Contracts\ProductCatalog\ViewMatcher\ProductBased\AttributeValue as AttributeValueInterface;

final class AttributeValue extends AbstractProductMatcher implements AttributeValueInterface
{
    /** @var array<string,mixed> */
    private array $values = [];

    public function setMatchingConfig($matchingConfig): void
    {
        $this->values = (array)$matchingConfig;
    }

    protected function matchProduct(ProductInterface $product): bool
    {
        foreach ($this->values as $identifier => $value) {
            $attribute = $this->findAttributeWithIdentifier($product, $identifier);

            if ($attribute === null) {
                return false;
            }

            if ($attribute->getValue() !== $value) {
                return false;
            }
        }

        return true;
    }

    private function findAttributeWithIdentifier(ProductInterface $product, string $identifier): ?AttributeInterface
    {
        foreach ($product->getAttributes() as $attribute) {
            if ($attribute->getIdentifier() === $identifier) {
                return $attribute;
            }
        }

        return null;
    }
}
