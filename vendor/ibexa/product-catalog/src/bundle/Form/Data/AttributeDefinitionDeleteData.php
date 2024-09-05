<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class AttributeDefinitionDeleteData
{
    /**
     * @Assert\NotBlank()
     */
    private ?AttributeDefinitionInterface $attributeDefinition;

    public function __construct(?AttributeDefinitionInterface $attributeDefinition = null)
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getAttributeDefinition(): ?AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function setAttributeDefinition(?AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->attributeDefinition = $attributeDefinition;
    }
}
