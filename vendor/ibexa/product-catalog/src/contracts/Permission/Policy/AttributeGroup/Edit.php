<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;

final class Edit extends AbstractAttributeGroupPolicy
{
    private const EDIT = 'edit';

    private ?AttributeGroupUpdateStruct $attributeGroupUpdateStruct;

    public function __construct(?AttributeGroupUpdateStruct $attributeGroupUpdateStruct = null)
    {
        $this->attributeGroupUpdateStruct = $attributeGroupUpdateStruct;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?AttributeGroupUpdateStruct
    {
        return $this->attributeGroupUpdateStruct;
    }
}
