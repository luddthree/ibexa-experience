<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Values\ProductType;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

class AssignAttributeDefinitionStruct extends ValueObject
{
    private AttributeDefinitionInterface $attributeDefinition;

    private bool $required;

    private bool $isDiscriminator;

    public function __construct(
        AttributeDefinitionInterface $attributeDefinition,
        bool $required = true,
        bool $isDiscriminator = false
    ) {
        parent::__construct();

        $this->attributeDefinition = $attributeDefinition;
        $this->required = $required;
        $this->isDiscriminator = $isDiscriminator;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function setAttributeDefinition(AttributeDefinitionInterface $attributeDefinition): void
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function isDiscriminator(): bool
    {
        return $this->isDiscriminator;
    }

    public function setDiscriminator(bool $isDiscriminator): void
    {
        $this->isDiscriminator = $isDiscriminator;
    }
}
