<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\Product;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Value as ProductSpecificationValue;
use LogicException;

class ProductUpdateStruct extends ValueObject
{
    /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface */
    private ProductInterface $product;

    private ?string $code = null;

    /** @var array<string,mixed> */
    private array $attributes = [];

    /**
     * The update structure for the product content.
     */
    private ContentUpdateStruct $contentUpdateStruct;

    /**
     * The update structure for the product metadata.
     */
    private ?ContentMetadataUpdateStruct $contentMetadataUpdateStruct = null;

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface $product
     */
    public function __construct(ProductInterface $product, ContentUpdateStruct $contentUpdateStruct)
    {
        parent::__construct();

        $this->product = $product;
        $this->contentUpdateStruct = $contentUpdateStruct;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\ProductInterface|\Ibexa\Contracts\ProductCatalog\Values\ContentAwareProductInterface
     */
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

    public function getContentUpdateStruct(): ContentUpdateStruct
    {
        return $this->contentUpdateStruct;
    }

    public function setContentUpdateStruct(ContentUpdateStruct $contentUpdateStruct): void
    {
        $this->contentUpdateStruct = $contentUpdateStruct;
    }

    public function getContentMetadataUpdateStruct(): ?ContentMetadataUpdateStruct
    {
        return $this->contentMetadataUpdateStruct;
    }

    public function setContentMetadataUpdateStruct(?ContentMetadataUpdateStruct $contentMetadataUpdateStruct): void
    {
        $this->contentMetadataUpdateStruct = $contentMetadataUpdateStruct;
    }

    /**
     * @param mixed $value
     */
    public function setField(string $fieldDefIdentifier, $value, ?string $language = null): void
    {
        if ($value instanceof ProductSpecificationValue) {
            throw new LogicException('Indirect modification of product specification is not allowed');
        }

        $this->contentUpdateStruct->setField($fieldDefIdentifier, $value, $language);
    }
}
