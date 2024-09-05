<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Local\Events;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\ProductCatalog\Local\Values\AttributeDefinition\AttributeDefinitionCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use UnexpectedValueException;

final class BeforeCreateAttributeDefinitionEvent extends BeforeEvent
{
    private AttributeDefinitionCreateStruct $createStruct;

    private ?AttributeDefinitionInterface $resultAttributeDefinition = null;

    public function __construct(AttributeDefinitionCreateStruct $createStruct)
    {
        $this->createStruct = $createStruct;
    }

    public function getCreateStruct(): AttributeDefinitionCreateStruct
    {
        return $this->createStruct;
    }

    public function getResultAttributeDefinition(): AttributeDefinitionInterface
    {
        if ($this->resultAttributeDefinition === null) {
            $message = 'Return value is not set or not of type %s. Check hasResultAttributeDefinition() or'
                . ' set it using setResultAttributeDefinition() before you call the getter.';

            throw new UnexpectedValueException(sprintf($message, AttributeDefinitionInterface::class));
        }

        return $this->resultAttributeDefinition;
    }

    public function hasResultAttributeDefinition(): bool
    {
        return $this->resultAttributeDefinition instanceof AttributeDefinitionInterface;
    }

    public function setResultAttributeDefinition(?AttributeDefinitionInterface $resultAttributeDefinition): void
    {
        $this->resultAttributeDefinition = $resultAttributeDefinition;
    }
}
