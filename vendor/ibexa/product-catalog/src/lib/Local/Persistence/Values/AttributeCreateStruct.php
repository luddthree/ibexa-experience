<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Values;

final class AttributeCreateStruct
{
    /** @var string|int */
    private $attributeDefinitionId;

    private int $productSpecificationId;

    /** @var object|scalar|array<mixed>|null */
    private $value;

    /**
     * @param string|int $attributeDefinitionId
     * @param object|scalar|array<mixed>|null $value
     */
    public function __construct(
        int $productSpecificationId,
        $attributeDefinitionId,
        $value
    ) {
        $this->productSpecificationId = $productSpecificationId;
        $this->attributeDefinitionId = $attributeDefinitionId;
        $this->value = $value;
    }

    public function getProductSpecificationId(): int
    {
        return $this->productSpecificationId;
    }

    /**
     * @return int|string
     */
    public function getAttributeDefinitionId()
    {
        return $this->attributeDefinitionId;
    }

    /**
     * @return object|scalar|array<mixed>|null
     */
    public function getValue()
    {
        return $this->value;
    }
}
