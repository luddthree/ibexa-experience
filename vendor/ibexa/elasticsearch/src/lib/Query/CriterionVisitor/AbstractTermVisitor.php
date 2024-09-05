<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;

/**
 * Abstract visitor for term criterions.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
 */
abstract class AbstractTermVisitor implements CriterionVisitor
{
    final public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $term = new TermQuery();
        $term->withField($this->getTargetField($criterion));
        $term->withValue($this->getTargetValue($criterion));

        return $term->toArray();
    }

    protected function getTargetValue(Criterion $criterion)
    {
        $value = $criterion->value;
        if (is_array($value)) {
            $value = array_values($value);
        }

        return $value;
    }

    abstract protected function getTargetField(Criterion $criterion): string;
}

class_alias(AbstractTermVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\AbstractTermVisitor');
