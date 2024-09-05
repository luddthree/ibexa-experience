<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\CatalogFilter;

use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface;
use Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;

final class FilterFormMapperRegistry implements FilterFormMapperRegistryInterface
{
    /** @var iterable<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface> */
    private iterable $mappers;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterFormMapperInterface> $mappers
     */
    public function __construct(iterable $mappers)
    {
        $this->mappers = $mappers;
    }

    public function hasMapper(FilterDefinitionInterface $filterDefinition): bool
    {
        return $this->findMapper($filterDefinition) !== null;
    }

    public function getMapper(FilterDefinitionInterface $filterDefinition): FilterFormMapperInterface
    {
        $mapper = $this->findMapper($filterDefinition);

        if ($mapper === null) {
            throw new InvalidArgumentException(
                '$fieldDefinition',
                sprintf(
                    'Undefined %s for filter definition with identifier "%s"',
                    FilterFormMapperInterface::class,
                    $filterDefinition->getIdentifier()
                )
            );
        }

        return $mapper;
    }

    private function findMapper(FilterDefinitionInterface $filterDefinition): ?FilterFormMapperInterface
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->supports($filterDefinition)) {
                return $mapper;
            }
        }

        return null;
    }
}
