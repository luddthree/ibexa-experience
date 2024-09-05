<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements  \Pagerfanta\Adapter\AdapterInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface
 * >
 */
final class AttributeGroupListAdapter implements AdapterInterface
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

    public function getNbResults(): int
    {
        $query = clone $this->query;
        $query->setLimit(0);

        return $this->attributeGroupService->findAttributeGroups($query)->getTotalCount();
    }

    /**
     * @param int $offset
     * @param int $length
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupListInterface
     */
    public function getSlice($offset, $length): iterable
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($length);

        return $this->attributeGroupService->findAttributeGroups($query);
    }
}
