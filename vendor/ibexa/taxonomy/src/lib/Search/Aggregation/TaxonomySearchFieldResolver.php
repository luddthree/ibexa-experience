<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\Aggregation;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Solr\Query\Common\AggregationVisitor\AggregationFieldResolver as SolrAggregationFieldResolver;
use Ibexa\Contracts\Taxonomy\Search\Query\Aggregation\TaxonomyEntryIdAggregation;
use Ibexa\Elasticsearch\Query\AggregationVisitor\AggregationFieldResolver as ElasticsearchAggregationFieldResolver;
use Webmozart\Assert\Assert;

final class TaxonomySearchFieldResolver implements SolrAggregationFieldResolver, ElasticsearchAggregationFieldResolver
{
    /**
     * @param \Ibexa\Contracts\Taxonomy\Search\Query\Aggregation\TaxonomyEntryIdAggregation $aggregation
     */
    public function resolveTargetField(Aggregation $aggregation): string
    {
        Assert::isInstanceOf($aggregation, TaxonomyEntryIdAggregation::class);

        return sprintf('taxonomy_entry_%s_mid', $aggregation->getTaxonomyIdentifier());
    }
}
