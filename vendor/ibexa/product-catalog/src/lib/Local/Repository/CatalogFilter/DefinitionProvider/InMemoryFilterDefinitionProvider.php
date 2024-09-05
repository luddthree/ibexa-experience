<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\ProductCatalog\Common\Pool;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Exception\UnknownFilterDefinitionException;

final class InMemoryFilterDefinitionProvider implements FilterDefinitionProviderInterface
{
    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface> */
    private Pool $pool;

    private ConfigResolverInterface $configResolver;

    /**
     * @param iterable<string, \Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface> $catalogFilters
     */
    public function __construct(
        iterable $catalogFilters,
        ConfigResolverInterface $configResolver
    ) {
        $this->pool = new Pool(FilterDefinitionInterface::class, $catalogFilters);
        $this->pool->setExceptionArgumentName('identifier');
        $this->pool->setExceptionMessageTemplate('Could not find %s with \'%s\' identifier');
        $this->configResolver = $configResolver;
    }

    public function hasFilterDefinition(string $identifier): bool
    {
        return $this->pool->has($identifier);
    }

    public function getFilterDefinition(string $identifier): FilterDefinitionInterface
    {
        try {
            return $this->pool->get($identifier);
        } catch (InvalidArgumentException $e) {
            throw new UnknownFilterDefinitionException($identifier);
        }
    }

    public function getFilterDefinitions(): iterable
    {
        return $this->pool->getEntries();
    }

    public function getDefaultFilterIdentifiers(): array
    {
        return array_unique($this->configResolver->getParameter('product_catalog.default_filters'));
    }
}
