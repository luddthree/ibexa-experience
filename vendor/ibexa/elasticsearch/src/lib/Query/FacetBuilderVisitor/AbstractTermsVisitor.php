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
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsAggregation;

/**
 * @deprecated since eZ Platform 3.2.0, to be removed in Ibexa 4.0.0.
 */
abstract class AbstractTermsVisitor implements FacetBuilderVisitor
{
    final public function visit(FacetBuilderVisitor $dispatcher, FacetBuilder $builder, LanguageFilter $languageFilter): array
    {
        $aggregation = new TermsAggregation($this->getTargetField($builder));
        $aggregation->withSize($builder->limit);
        $aggregation->withMinDocCount($builder->minCount);

        return $aggregation->toArray();
    }

    abstract protected function getTargetField(FacetBuilder $builder): string;
}

class_alias(AbstractTermsVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\FacetBuilderVisitor\AbstractTermsVisitor');
