<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;

final class Create extends AbstractAttributeDefinitionPolicy
{
    private const CREATE = 'create';

    private ?AttributeDefinitionCreateStruct $attributeDefinitionCreateStruct;

    public function __construct(?AttributeDefinitionCreateStruct $attributeDefinitionCreateStruct = null)
    {
        $this->attributeDefinitionCreateStruct = $attributeDefinitionCreateStruct;
    }

    public function getFunction(): string
    {
        return self::CREATE;
    }

    public function getObject(): ?AttributeDefinitionCreateStruct
    {
        return $this->attributeDefinitionCreateStruct;
    }
}
