<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Rest\Value;

final class AttributeView extends Value
{
    private string $identifier;

    private AttributeDefinitionListInterface $attributeDefinitionList;

    public function __construct(
        string $identifier,
        AttributeDefinitionListInterface $attributeDefinitionList
    ) {
        $this->identifier = $identifier;
        $this->attributeDefinitionList = $attributeDefinitionList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAttributeDefinitionList(): AttributeDefinitionListInterface
    {
        return $this->attributeDefinitionList;
    }
}
