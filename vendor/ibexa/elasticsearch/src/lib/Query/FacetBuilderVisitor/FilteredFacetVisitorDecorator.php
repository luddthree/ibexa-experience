<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\FilterAggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RawAggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RawQuery;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class FilteredFacetVisitorDecorator implements FacetBuilderVisitor
{
    public const INNER_AGGREGATION_KEY = 'inner_f';

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor */
    private $innerVisitor;

    /** @var \Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor */
    private $criterionVisitor;

    public function __construct(FacetBuilderVisitor $innerVisitor, CriterionVisitor $criterionVisitor)
    {
        $this->innerVisitor = $innerVisitor;
        $this->criterionVisitor = $criterionVisitor;
    }

    public function supports(FacetBuilder $builder, LanguageFilter $languageFilter): bool
    {
        return $this->innerVisitor->supports($builder, $languageFilter);
    }

    public function visit(FacetBuilderVisitor $dispatcher, FacetBuilder $builder, LanguageFilter $languageFilter): array
    {
        $aggregation = $this->innerVisitor->visit($dispatcher, $builder, $languageFilter);

        if ($builder->filter instanceof Criterion) {
            $filter = $this->criterionVisitor->visit($this->criterionVisitor, $builder->filter, $languageFilter);

            $wrappedAggregation = new FilterAggregation(
                new RawQuery($filter),
                [
                    self::INNER_AGGREGATION_KEY => new RawAggregation($aggregation),
                ]
            );

            $aggregation = $wrappedAggregation->toArray();
        }

        return $aggregation;
    }
}

class_alias(FilteredFacetVisitorDecorator::class, 'Ibexa\Platform\ElasticSearchEngine\Query\FacetBuilderVisitor\FilteredFacetVisitorDecorator');
