<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class UpdateAttributeGroupEvent extends AfterEvent
{
    private AttributeGroupInterface $attributeGroup;

    private AttributeGroupUpdateStruct $updateStruct;

    public function __construct(AttributeGroupInterface $attributeGroup, AttributeGroupUpdateStruct $updateStruct)
    {
        $this->attributeGroup = $attributeGroup;
        $this->updateStruct = $updateStruct;
    }

    public function getAttributeGroup(): AttributeGroupInterface
    {
        return $this->attributeGroup;
    }

    public function getUpdateStruct(): AttributeGroupUpdateStruct
    {
        return $this->updateStruct;
    }
}
