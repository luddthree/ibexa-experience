<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\AggregationVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation;
use Ibexa\Contracts\Elasticsearch\Query\AggregationVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\FilterAggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsAggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\WildcardQuery;

final class SubtreeTermAggregationVisitor implements AggregationVisitor
{
    /** @var string */
    private $pathStringFieldName;

    /** @var string */
    private $locationIdFieldName;

    public function __construct(string $pathStringFieldName, string $locationIdFieldName)
    {
        $this->pathStringFieldName = $pathStringFieldName;
        $this->locationIdFieldName = $locationIdFieldName;
    }

    public function supports(Aggregation $aggregation, LanguageFilter $languageFilter): bool
    {
        return $aggregation instanceof SubtreeTermAggregation;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Aggregation\Location\SubtreeTermAggregation $aggregation
     */
    public function visit(
        AggregationVisitor $dispatcher,
        Aggregation $aggregation,
        LanguageFilter $languageFilter
    ): array {
        $pathString = $aggregation->getPathString();

        $subtreeAggregation = new TermsAggregation($this->locationIdFieldName);
        $subtreeAggregation->withSize(
            $aggregation->getLimit() + $this->getPathLevel($pathString)
        );
        $subtreeAggregation->withMinDocCount($aggregation->getMinCount());

        $subtreeFilter = new FilterAggregation();
        $subtreeFilter->addAggregation('nested', $subtreeAggregation);
        $subtreeFilter->withQuery(
            new WildcardQuery(
                $this->pathStringFieldName,
                $this->getSubtreeWildcard($pathString)
            )
        );

        return $subtreeFilter->toArray();
    }

    private function getSubtreeWildcard(string $pathString): string
    {
        return str_replace('/', '\\/', $pathString) . '?*';
    }

    private function getPathLevel(string $pathString): int
    {
        return count(explode('/', trim($pathString, '/')));
    }
}

class_alias(SubtreeTermAggregationVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\AggregationVisitor\SubtreeTermAggregationVisitor');
