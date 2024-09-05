<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class UpdateAttributeDefinitionEvent extends AfterEvent
{
    private AttributeDefinitionInterface $attributeDefinition;

    private AttributeDefinitionUpdateStruct $updateStruct;

    public function __construct(
        AttributeDefinitionInterface $attributeDefinition,
        AttributeDefinitionUpdateStruct $updateStruct
    ) {
        $this->attributeDefinition = $attributeDefinition;
        $this->updateStruct = $updateStruct;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }

    public function getUpdateStruct(): AttributeDefinitionUpdateStruct
    {
        return $this->updateStruct;
    }
}
