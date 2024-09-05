<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;

final class Edit extends AbstractAttributeDefinitionPolicy
{
    private const EDIT = 'edit';

    private ?AttributeDefinitionUpdateStruct $attributeDefinitionUpdateStruct;

    public function __construct(?AttributeDefinitionUpdateStruct $attributeDefinitionUpdateStruct = null)
    {
        $this->attributeDefinitionUpdateStruct = $attributeDefinitionUpdateStruct;
    }

    public function getFunction(): string
    {
        return self::EDIT;
    }

    public function getObject(): ?AttributeDefinitionUpdateStruct
    {
        return $this->attributeDefinitionUpdateStruct;
    }
}
