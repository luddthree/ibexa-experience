<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @implements \Symfony\Component\Form\DataTransformerInterface<LogicalAnd, array>
 */
final class CatalogFilterTransformer implements DataTransformerInterface
{
    /** @var \Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface[] */
    private iterable $catalogFilters;

    /**
     * @param iterable<\Ibexa\Contracts\ProductCatalog\CatalogFilters\FilterDefinitionInterface> $catalogFilters $catalogFilters
     */
    public function __construct(iterable $catalogFilters)
    {
        $this->catalogFilters = $catalogFilters;
    }

    /**
     * @return array<string, \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface>
     */
    public function transform($value): array
    {
        if ($value === null) {
            return [];
        }

        if (!$value instanceof LogicalAnd) {
            throw new TransformationFailedException('Expected a ' . LogicalAnd::class . ' instance');
        }

        $result = [];
        $criteria = $value->getCriteria();
        foreach ($criteria as $criterion) {
            foreach ($this->catalogFilters as $catalogFilter) {
                if ($catalogFilter->supports($criterion)) {
                    $result[$catalogFilter->getIdentifier()] = $criterion;
                }
            }
        }

        return $result;
    }

    /**
     * @param array<\Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface>|null $value
     */
    public function reverseTransform($value): ?LogicalAnd
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Invalid data, expected an array value');
        }

        $value = array_filter($value);
        if (empty($value)) {
            return null;
        }

        return new LogicalAnd($value);
    }
}
