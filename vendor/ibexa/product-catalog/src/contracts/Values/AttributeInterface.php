<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values;

interface AttributeInterface
{
    public function getIdentifier(): string;

    public function getAttributeDefinition(): AttributeDefinitionInterface;

    /**
     * @return mixed
     */
    public function getValue();
}
