<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;

final class CreateAttributeGroupEvent extends AfterEvent
{
    private AttributeGroupCreateStruct $createStruct;

    private AttributeGroupInterface $attributeGroup;

    public function __construct(AttributeGroupCreateStruct $createStruct, AttributeGroupInterface $attributeGroup)
    {
        $this->createStruct = $createStruct;
        $this->attributeGroup = $attributeGroup;
    }

    public function getCreateStruct(): AttributeGroupCreateStruct
    {
        return $this->createStruct;
    }

    public function getAttributeGroup(): AttributeGroupInterface
    {
        return $this->attributeGroup;
    }
}
