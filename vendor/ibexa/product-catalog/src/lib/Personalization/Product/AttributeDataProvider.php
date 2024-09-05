<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Personalization\Product;

use Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface;

final class AttributeDataProvider implements DataProviderInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\Personalization\AttributeConverterInterface[] */
    private iterable $attributeConverters;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\Personalization\AttributeConverterInterface> $attributeConverters
     */
    public function __construct(iterable $attributeConverters)
    {
        $this->attributeConverters = $attributeConverters;
    }

    private const DATA_KEY = 'product_attribute';

    public function getData(ContentAwareProductInterface $product, string $languageCode): ?array
    {
        $attributes = [];
        foreach ($product->getAttributes() as $attribute) {
            $attributeValue = $attribute->getValue();
            foreach ($this->attributeConverters as $attributeConverter) {
                if ($attributeConverter->accept($attribute)) {
                    $attributeValue = $attributeConverter->convert($attribute);
                }
            }

            if (!is_scalar($attributeValue) && !is_array($attributeValue)) {
                continue;
            }

            $attributes[self::DATA_KEY . '_' . $attribute->getIdentifier()] = $attributeValue;
        }

        return $attributes;
    }
}
