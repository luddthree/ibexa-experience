<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;

final class Create extends AbstractAttributeGroupPolicy
{
    private const CREATE = 'create';

    private ?AttributeGroupCreateStruct $attributeGroupCreateStruct;

    public function __construct(?AttributeGroupCreateStruct $attributeGroupCreateStruct = null)
    {
        $this->attributeGroupCreateStruct = $attributeGroupCreateStruct;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?AttributeGroupCreateStruct
    {
        return $this->attributeGroupCreateStruct;
    }
}
