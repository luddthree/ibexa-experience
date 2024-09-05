<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Aggregation\AttributeAggregationInterface;
use Ibexa\Contracts\Solr\Query\Common\AggregationVisitor\AggregationFieldResolver as SolrAggregationFieldResolver;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver as ElasticsearchAggregationFieldResolver;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\FieldNameBuilder\AttributeFieldNameBuilder;
use InvalidArgumentException;

final class AttributeAggregationFieldResolver implements ElasticsearchAggregationFieldResolver, SolrAggregationFieldResolver
{
    private string $indexSuffix;

    private string $searchIndexFieldName;

    /**
     * @phpstan-param non-empty-string $indexSuffix
     * @phpstan-param non-empty-string $searchIndexFieldName
     */
    public function __construct(
        string $indexSuffix,
        string $searchIndexFieldName = 'value'
    ) {
        $this->indexSuffix = $indexSuffix;
        $this->searchIndexFieldName = $searchIndexFieldName;
    }

    public function resolveTargetField(Aggregation $aggregation): string
    {
        if (!$aggregation instanceof AttributeAggregationInterface) {
            throw new InvalidArgumentException(sprintf(
                'Expected aggregation of class %s, received %s.',
                AttributeAggregationInterface::class,
                get_class($aggregation),
            ));
        }

        $fieldNameBuilder = new AttributeFieldNameBuilder($aggregation->getAttributeDefinitionIdentifier());
        $fieldNameBuilder->withField($this->searchIndexFieldName);

        return $fieldNameBuilder->build() . '_' . $this->indexSuffix;
    }
}
