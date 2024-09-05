<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\FacetBuilderVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\FacetBuilder;
use Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\GlobalAggregation;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RawAggregation;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
final class GlobalFacetVisitorDecorator implements FacetBuilderVisitor
{
    public const INNER_AGGREGATION_KEY = 'inner_g';

    /** @var \Ibexa\Contracts\Elasticsearch\Query\FacetBuilderVisitor */
    private $innerVisitor;

    public function __construct(FacetBuilderVisitor $innerVisitor)
    {
        $this->innerVisitor = $innerVisitor;
    }

    public function supports(FacetBuilder $builder, LanguageFilter $languageFilter): bool
    {
        return $this->innerVisitor->supports($builder, $languageFilter);
    }

    public function visit(FacetBuilderVisitor $dispatcher, FacetBuilder $builder, LanguageFilter $languageFilter): array
    {
        $aggregation = $this->innerVisitor->visit($dispatcher, $builder, $languageFilter);

        if ($builder->global) {
            $wrappedAggregation = new GlobalAggregation([
                self::INNER_AGGREGATION_KEY => new RawAggregation($aggregation),
            ]);

            $aggregation = $wrappedAggregation->toArray();
        }

        return $aggregation;
    }
}

class_alias(GlobalFacetVisitorDecorator::class, 'Ibexa\Platform\ElasticSearchEngine\Query\FacetBuilderVisitor\GlobalFacetVisitorDecorator');
