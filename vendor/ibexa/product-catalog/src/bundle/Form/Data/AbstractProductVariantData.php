<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductCode;
use Ibexa\Bundle\ProductCatalog\Validator\Constraints\UniqueProductVariantAttributeCombination;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @UniqueProductCode()
 *
 * @UniqueProductVariantAttributeCombination()
 */
abstract class AbstractProductVariantData implements ProductCodeDataContainerInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=0, max=ProductSpecificationType::PRODUCT_CODE_MAX_LENGTH)
     * @Assert\Regex(pattern=ProductSpecificationType::PRODUCT_CODE_PATTERN)
     */
    private ?string $code = null;

    /**
     * @Assert\Valid
     *
     * @var \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[]
     */
    private iterable $attributes = [];

    private ProductInterface $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return iterable<\Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData>
     */
    public function getAttributes(): iterable
    {
        return $this->attributes;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\Form\Data\AttributeData[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }
}
