<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Values;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class AttributeDefinitionAssignment implements AttributeDefinitionAssignmentInterface
{
    private AttributeDefinitionInterface $attributeDefinition;

    private bool $required;

    private bool $discriminator;

    public function __construct(
        AttributeDefinitionInterface $attributeDefinition,
        bool $required = false,
        bool $discriminator = false
    ) {
        $this->attributeDefinition = $attributeDefinition;
        $this->required = $required;
        $this->discriminator = $discriminator;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isDiscriminator(): bool
    {
        return $this->discriminator;
    }
}
