<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Exception\UnknownFilterDefinitionException;

final class ChainFilterDefinitionProvider implements FilterDefinitionProviderInterface
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface> */
    private iterable $providers;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface> $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = $providers;
    }

    public function hasFilterDefinition(string $identifier): bool
    {
        foreach ($this->providers as $provider) {
            if ($provider->hasFilterDefinition($identifier)) {
                return true;
            }
        }

        return false;
    }

    public function getFilterDefinition(string $identifier): FilterDefinitionInterface
    {
        foreach ($this->providers as $provider) {
            if ($provider->hasFilterDefinition($identifier)) {
                return $provider->getFilterDefinition($identifier);
            }
        }

        throw new UnknownFilterDefinitionException($identifier);
    }

    public function getFilterDefinitions(): iterable
    {
        foreach ($this->providers as $provider) {
            yield from $provider->getFilterDefinitions();
        }
    }

    public function getDefaultFilterIdentifiers(): array
    {
        $defaultFilterIdentifiers = [];
        foreach ($this->providers as $provider) {
            $defaultFilterIdentifiers[] = $provider->getDefaultFilterIdentifiers();
        }

        return array_unique(array_merge(...$defaultFilterIdentifiers));
    }
}
