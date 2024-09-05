<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Value;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface;
use Ibexa\Rest\Value;

final class AttributeGroupView extends Value
{
    private string $identifier;

    private AttributeGroupListInterface $attributeGroupList;

    public function __construct(string $identifier, AttributeGroupListInterface $attributeGroupList)
    {
        $this->identifier = $identifier;
        $this->attributeGroupList = $attributeGroupList;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getAttributeGroupList(): AttributeGroupListInterface
    {
        return $this->attributeGroupList;
    }
}
