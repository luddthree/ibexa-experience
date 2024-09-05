<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class BeforeDeleteAttributeGroupEvent extends BeforeEvent
{
    private AttributeGroupInterface $attributeGroup;

    public function __construct(AttributeGroupInterface $attributeGroup)
    {
        $this->attributeGroup = $attributeGroup;
    }

    public function getAttributeGroup(): AttributeGroupInterface
    {
        return $this->attributeGroup;
    }
}
