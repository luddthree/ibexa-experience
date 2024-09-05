<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class AttributeData
{
    private ?AttributeDefinitionInterface $attributeDefinition;

    /** @var object|scalar|array<mixed>|null */
    private $value;

    /**
     * @param object|scalar|array<mixed>|null $value
     */
    public function __construct(?AttributeDefinitionInterface $attributeDefinition = null, $value = null)
    {
        $this->attributeDefinition = $attributeDefinition;
        $this->value = $value;
    }

    public function getAttributeDefinition(): ?AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function setAttributeDefinition(?AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    /**
     * @return object|scalar|array<mixed>|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param object|scalar|array<mixed>|null $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }
}
