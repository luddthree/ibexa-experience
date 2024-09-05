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
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;

/**
 * Abstract visitor for terms criterions.
 *
 * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
 */
abstract class AbstractTermsVisitor implements CriterionVisitor
{
    final public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $terms = new TermsQuery();
        $terms->withField($this->getTargetField($criterion));
        $terms->withValue($this->getTargetValue($criterion->value));

        return $terms->toArray();
    }

    protected function getTargetValue($value)
    {
        if (is_array($value)) {
            $value = array_values($value);
        } else {
            $value = (array)$value;
        }

        return $value;
    }

    abstract protected function getTargetField(Criterion $criterion): string;
}

class_alias(AbstractTermsVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\AbstractTermsVisitor');
