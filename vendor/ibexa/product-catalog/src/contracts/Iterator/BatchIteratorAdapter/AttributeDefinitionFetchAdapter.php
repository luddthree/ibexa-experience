<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIteratorAdapter;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Iterator;

final class AttributeDefinitionFetchAdapter implements BatchIteratorAdapter
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

    /**
     * @return \Iterator<mixed,\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface>
     */
    public function fetch(int $offset, int $limit): Iterator
    {
        $query = clone $this->query;
        $query->setOffset($offset);
        $query->setLimit($limit);

        /** @var \ArrayIterator<int,\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface> */
        return $this->attributeDefinitionService->findAttributesDefinitions($query)->getIterator();
    }
}
