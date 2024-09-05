<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Pagerfanta\Adapter;

use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionListInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Pagerfanta\Adapter\AdapterInterface;

/**
 * @implements \Pagerfanta\Adapter\AdapterInterface<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface>
 */
final class AttributeDefinitionListAdapter implements AdapterInterface
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeDefinitionQuery $query;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        ?AttributeDefinitionQuery $query = null
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->query = $query ?? new AttributeDefinitionQuery();
    }

    public function getNbResults(): int
    {
        $query = clone $this->query;
        $query->setLimit(0);

        return $this->attributeDefinitionService->findAttributesDefinitions($query)->getTotalCount();
    }

    public function getSlice($offset, $length): AttributeDefinitionListInterface
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($length);

        return $this->attributeDefinitionService->findAttributesDefinitions($query);
    }
}
