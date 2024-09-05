<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\ProductVariantInterface;

final class ProductVariantUpdateData extends AbstractProductVariantData
{
    private string $originalCode;

    public function __construct(ProductVariantInterface $variant)
    {
        parent::__construct($variant->getBaseProduct());
        $this->originalCode = $variant->getCode();
        $this->setCode($variant->getCode());

        $attributesData = [];
        foreach ($variant->getDiscriminatorAttributes() as $attribute) {
            $attributesData[$attribute->getIdentifier()] = new AttributeData(
                $attribute->getAttributeDefinition(),
                $attribute->getValue()
            );
        }

        $this->setAttributes($attributesData);
    }

    public function getOriginalCode(): string
    {
        return $this->originalCode;
    }
}
