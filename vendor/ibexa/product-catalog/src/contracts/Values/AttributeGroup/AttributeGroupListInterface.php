<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\AttributeGroup;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use IteratorAggregate;

/**
 * @extends IteratorAggregate<int,AttributeGroupInterface>
 */
interface AttributeGroupListInterface extends IteratorAggregate
{
    /**
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface[]
     */
    public function getAttributeGroups(): array;

    /**
     * Return total count of found attribute groups (filtered by permissions).
     */
    public function getTotalCount(): int;
}
