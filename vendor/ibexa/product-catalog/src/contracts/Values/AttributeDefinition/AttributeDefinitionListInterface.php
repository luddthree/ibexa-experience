<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition;

use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int,AttributeDefinitionInterface>
 */
interface AttributeDefinitionListInterface extends IteratorAggregate
{
    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface[]
     */
    public function getAttributeDefinitions(): array;

    /**
     * Return total count of found attribute (filtered by permissions).
     */
    public function getTotalCount(): int;
}
