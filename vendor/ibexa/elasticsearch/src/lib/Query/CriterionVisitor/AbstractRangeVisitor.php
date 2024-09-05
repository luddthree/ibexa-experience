<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;

abstract class AbstractRangeVisitor implements CriterionVisitor
{
    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        return $this->getRangeExpression(
            $this->getTargetField($criterion),
            $criterion->operator,
            $this->getTargetValue($criterion)
        );
    }

    abstract protected function getTargetField(Criterion $criterion): string;

    protected function getTargetValue(Criterion $criterion): array
    {
        if (!is_array($criterion->value)) {
            return [$criterion->value, null];
        }

        return [
            $criterion->value[0],
            $criterion->value[1] ?? null,
        ];
    }

    protected function getRangeExpression(string $field, string $operator, array $value): array
    {
        $query = new RangeQuery();
        $query->withField($field);
        $query->withOperator($operator);
        $query->withRange(...$value);

        return $query->toArray();
    }

    protected function isRangeOperator(string $operator): bool
    {
        return $operator === Operator::LT
            || $operator === Operator::LTE
            || $operator === Operator::GT
            || $operator === Operator::GTE
            || $operator === Operator::BETWEEN;
    }
}

class_alias(AbstractRangeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\AbstractRangeVisitor');
