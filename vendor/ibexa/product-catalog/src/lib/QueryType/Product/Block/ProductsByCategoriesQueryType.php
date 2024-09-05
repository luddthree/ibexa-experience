<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\QueryType\Product\Block;

use Ibexa\Contracts\ProductCatalog\QueryTypeInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\Taxonomy\Search\Query\Aggregation\TaxonomyEntryIdAggregation;

final class ProductsByCategoriesQueryType implements QueryTypeInterface
{
    public const DEFAULT_AGGREGATION_NAME = 'categories_distribution';
    public const DEFAULT_AGGREGATION_LIMIT = 10;

    private string $productTaxonomyIdentifier;

    public function __construct(string $productTaxonomyIdentifier)
    {
        $this->productTaxonomyIdentifier = $productTaxonomyIdentifier;
    }

    public function getQuery(array $parameters = []): ProductQuery
    {
        $aggregation = new TaxonomyEntryIdAggregation(
            $parameters['name'] ?? self::DEFAULT_AGGREGATION_NAME,
            $this->productTaxonomyIdentifier
        );
        $aggregation->setLimit($parameters['limit'] ?? self::DEFAULT_AGGREGATION_LIMIT);

        $query = new ProductQuery();
        $query->setLimit(0);
        $query->setAggregations([$aggregation]);

        return $query;
    }

    public function getSupportedParameters(): array
    {
        return ['aggregation_name', 'aggregation_limit'];
    }

    public static function getName(): string
    {
        return 'ibexa:blocks:categories_distribution';
    }
}
