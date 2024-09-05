<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;

class ProductTypeUpdateStruct extends ValueObject
{
    private ProductTypeInterface $productType;

    private ContentTypeUpdateStruct $contentTypeUpdateStruct;

    /** @var \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] */
    private array $attributeDefinitionStructs = [];

    private bool $isVirtual = false;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentTypeUpdateStruct $contentTypeUpdateStruct
     */
    public function __construct(
        ProductTypeInterface $productType,
        ContentTypeUpdateStruct $contentTypeUpdateStruct
    ) {
        parent::__construct();

        $this->productType = $productType;
        $this->contentTypeUpdateStruct = $contentTypeUpdateStruct;
    }

    public function getProductType(): ProductTypeInterface
    {
        return $this->productType;
    }

    public function getContentTypeUpdateStruct(): ContentTypeUpdateStruct
    {
        return $this->contentTypeUpdateStruct;
    }

    public function setContentTypeUpdateStruct(ContentTypeUpdateStruct $contentTypeUpdateStruct): void
    {
        $this->contentTypeUpdateStruct = $contentTypeUpdateStruct;
    }

    /**
     * @return \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[]
     */
    public function getAttributeDefinitionStructs(): iterable
    {
        return $this->attributeDefinitionStructs;
    }

    /**
     * @param \Ibexa\Contracts\ProductCatalog\Local\Values\ProductType\AssignAttributeDefinitionStruct[] $attributeDefinitionStructs
     */
    public function setAttributeDefinitionStructs(array $attributeDefinitionStructs): void
    {
        $this->attributeDefinitionStructs = $attributeDefinitionStructs;
    }

    public function addAttributeDefinition(AssignAttributeDefinitionStruct $struct): void
    {
        $this->attributeDefinitionStructs[] = $struct;
    }

    public function removeAttributeDefinition(AssignAttributeDefinitionStruct $struct): void
    {
        foreach ($this->attributeDefinitionStructs as $key => $attributeDefinitionStruct) {
            if ($attributeDefinitionStruct === $struct) {
                unset($this->attributeDefinitionStructs[$key]);
            }
        }
    }

    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    public function setVirtual(bool $isVirtual): void
    {
        $this->isVirtual = $isVirtual;
    }
}
