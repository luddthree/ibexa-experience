<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Iterator;

final class AttributeGroupFetchAdapter implements BatchIteratorAdapter
{
    private AttributeGroupServiceInterface $attributeGroupService;

    private AttributeGroupQuery $query;

    public function __construct(
        AttributeGroupServiceInterface $attributeGroupService,
        ?AttributeGroupQuery $query = null
    ) {
        $this->attributeGroupService = $attributeGroupService;
        $this->query = $query ?? new AttributeGroupQuery();
    }

    /**
     * @return \Iterator<mixed,\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($limit);

        /** @var \ArrayIterator<int,\Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface> */
        return $this->attributeGroupService->findAttributeGroups($query)->getIterator();
    }
}
