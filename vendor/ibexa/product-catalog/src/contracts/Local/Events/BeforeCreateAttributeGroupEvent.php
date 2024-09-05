<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeGroup\AttributeGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use UnexpectedValueException;

final class BeforeCreateAttributeGroupEvent extends BeforeEvent
{
    private AttributeGroupCreateStruct $createStruct;

    private ?AttributeGroupInterface $resultAttributeGroup = null;

    public function __construct(AttributeGroupCreateStruct $createStruct)
    {
        $this->createStruct = $createStruct;
    }

    public function getCreateStruct(): AttributeGroupCreateStruct
    {
        return $this->createStruct;
    }

    public function getResultAttributeGroup(): AttributeGroupInterface
    {
        if ($this->resultAttributeGroup === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultAttributeGroup() or'
                . ' set it using setResultAttributeGroup() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, AttributeGroupInterface::class));
        }

        return $this->resultAttributeGroup;
    }

    public function hasResultAttributeGroup(): bool
    {
        return $this->resultAttributeGroup instanceof AttributeGroupInterface;
    }

    public function setResultAttributeGroup(?AttributeGroupInterface $resultAttributeGroup): void
    {
        $this->resultAttributeGroup = $resultAttributeGroup;
    }
}
