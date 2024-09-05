<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class CreateAttributeDefinitionEvent extends AfterEvent
{
    private AttributeDefinitionCreateStruct $createStruct;

    private AttributeDefinitionInterface $attributeDefinition;

    public function __construct(
        AttributeDefinitionCreateStruct $createStruct,
        AttributeDefinitionInterface $attributeDefinition
    ) {
        $this->createStruct = $createStruct;
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getCreateStruct(): AttributeDefinitionCreateStruct
    {
        return $this->createStruct;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }
}
