<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\Attribute\FilterDefinitionFactoryInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionProviderInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\AttributeDefinitionFetchAdapter;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\ProductCatalog\Common\Pool;
use Ibexa\ProductCatalog\Local\Repository\CatalogFilter\DefinitionProvider\Exception\UnknownFilterDefinitionException;

final class AttributeFilterDefinitionProvider implements FilterDefinitionProviderInterface
{
    private const ATTRIBUTE_FILTER_PREFIX = 'product_attribute_';

    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    /** @var \Ibexa\ProductCatalog\Common\Pool<\Ibexa\Contracts\ProductCatalog\CatalogFilters\Attribute\FilterDefinitionFactoryInterface> */
    private Pool $filterDefinitionFactories;

    /**
     * @param iterable<string,\Ibexa\Contracts\ProductCatalog\CatalogFilters\Attribute\FilterDefinitionFactoryInterface> $filterDefinitionFactories
     */
    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        iterable $filterDefinitionFactories = []
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->filterDefinitionFactories = new Pool(FilterDefinitionFactoryInterface::class, $filterDefinitionFactories);
    }

    public function hasFilterDefinition(string $identifier): bool
    {
        if ($this->isAttributeDefinitionFilterIdentifier($identifier)) {
            try {
                $definition = $this->attributeDefinitionService->getAttributeDefinition(
                    $this->getAttributeDefinitionIdentifier($identifier)
                );

                return $this->isSupported($definition);
            } catch (NotFoundException $e) {
                return false;
            }
        }

        return false;
    }

    public function getFilterDefinition(string $identifier): FilterDefinitionInterface
    {
        if ($this->isAttributeDefinitionFilterIdentifier($identifier)) {
            $definition = $this->attributeDefinitionService->getAttributeDefinition(
                $this->getAttributeDefinitionIdentifier($identifier)
            );

            if ($this->isSupported($definition)) {
                return $this->createFilterDefinition($definition);
            }
        }

        throw new UnknownFilterDefinitionException($identifier);
    }

    public function getFilterDefinitions(): iterable
    {
        $attributeDefinitions = new BatchIterator(
            new AttributeDefinitionFetchAdapter(
                $this->attributeDefinitionService
            )
        );

        foreach ($attributeDefinitions as $attributeDefinition) {
            if ($this->isSupported($attributeDefinition)) {
                yield $this->createFilterDefinition($attributeDefinition);
            }
        }
    }

    public function getDefaultFilterIdentifiers(): array
    {
        return [];
    }

    private function createFilterDefinition(
        AttributeDefinitionInterface $attributeDefinition
    ): FilterDefinitionInterface {
        $factory = $this->filterDefinitionFactories->get(
            $attributeDefinition->getType()->getIdentifier()
        );

        return $factory->createFilterDefinition(
            $attributeDefinition,
            $this->getFilterDefinitionIdentifier($attributeDefinition)
        );
    }

    private function getAttributeDefinitionIdentifier(string $filterIdentifier): string
    {
        return substr($filterIdentifier, strlen(self::ATTRIBUTE_FILTER_PREFIX));
    }

    private function getFilterDefinitionIdentifier(AttributeDefinitionInterface $attributeDefinition): string
    {
        return self::ATTRIBUTE_FILTER_PREFIX . $attributeDefinition->getIdentifier();
    }

    private function isAttributeDefinitionFilterIdentifier(string $filterIdentifier): bool
    {
        return str_starts_with($filterIdentifier, self::ATTRIBUTE_FILTER_PREFIX);
    }

    private function isSupported(AttributeDefinitionInterface $definition): bool
    {
        return $this->filterDefinitionFactories->has($definition->getType()->getIdentifier());
    }
}
