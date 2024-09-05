<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\CatalogFilters;

interface FilterDefinitionProviderInterface
{
    public function hasFilterDefinition(string $identifier): bool;

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getFilterDefinition(string $identifier): FilterDefinitionInterface;

    /**
     * @return \Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface[]
     */
    public function getFilterDefinitions(): iterable;

    /**
     * @return string[]
     */
    public function getDefaultFilterIdentifiers(): array;
}
