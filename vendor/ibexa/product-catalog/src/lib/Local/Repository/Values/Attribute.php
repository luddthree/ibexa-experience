<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

final class Attribute implements AttributeInterface
{
    private AttributeDefinitionInterface $attributeDefinition;

    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct(AttributeDefinitionInterface $attributeDefinition, $value)
    {
        $this->attributeDefinition = $attributeDefinition;
        $this->value = $value;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function getIdentifier(): string
    {
        return $this->attributeDefinition->getIdentifier();
    }

    public function getValue()
    {
        return $this->value;
    }
}
