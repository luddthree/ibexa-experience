<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value as ProductSpecificationValue;
use LogicException;

class ProductCreateStruct extends ValueObject
{
    private ProductTypeInterface $productType;

    private ContentCreateStruct $contentCreateStruct;

    private ?string $code;

    /** @var array<string,mixed> */
    private array $attributes = [];

    public function __construct(
        ProductTypeInterface $productType,
        ContentCreateStruct $contentCreateStruct,
        ?string $code = null
    ) {
        parent::__construct();
        $this->productType = $productType;
        $this->contentCreateStruct = $contentCreateStruct;
        $this->code = $code;
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
     * @return array<string,mixed>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param mixed $value
     */
    public function setAttribute(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    /**
     * @param mixed $value
     */
    public function setField(string $fieldDefIdentifier, $value, ?string $language = null): void
    {
        if ($value instanceof ProductSpecificationValue) {
            throw new LogicException('Indirect modification of product specification is not allowed');
        }

        $this->contentCreateStruct->setField($fieldDefIdentifier, $value, $language);
    }

    public function getContentCreateStruct(): ContentCreateStruct
    {
        return $this->contentCreateStruct;
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->productType;
    }
}
