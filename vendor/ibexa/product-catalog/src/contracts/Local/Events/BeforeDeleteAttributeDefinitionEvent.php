<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;

final class BeforeDeleteAttributeDefinitionEvent extends BeforeEvent
{
    private AttributeDefinitionInterface $attributeDefinition;

    public function __construct(AttributeDefinitionInterface $attributeDefinition)
    {
        $this->attributeDefinition = $attributeDefinition;
    }

    public function getAttributeDefinition(): AttributeDefinitionInterface
    {
        return $this->attributeDefinition;
    }
}
