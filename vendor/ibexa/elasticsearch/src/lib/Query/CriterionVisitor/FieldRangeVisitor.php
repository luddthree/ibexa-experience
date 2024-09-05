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
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Core\Search\Common\FieldValueMapper;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RangeQuery;
use Ibexa\Elasticsearch\Query\CriterionVisitor\Iterator\FieldCriterionTargetIterator;

final class FieldRangeVisitor implements CriterionVisitor
{
    /** @var \Ibexa\Core\Search\Common\FieldNameResolver */
    private $fieldNameResolver;

    /** @var \Ibexa\Core\Search\Common\FieldValueMapper */
    private $fieldValueMapper;

    public function __construct(FieldNameResolver $fieldNameResolver, FieldValueMapper $fieldValueMapper)
    {
        $this->fieldNameResolver = $fieldNameResolver;
        $this->fieldValueMapper = $fieldValueMapper;
    }

    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\Field && $this->isRangeOperator($criterion->operator);
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $iterator = new FieldCriterionTargetIterator(
            $this->fieldNameResolver,
            $this->fieldValueMapper,
            $criterion
        );

        $query = new BoolQuery();
        foreach ($iterator as $name => $value) {
            $query->addShould(
                new RangeQuery($name, $criterion->operator, (array)$value)
            );
        }

        return $query->toArray();
    }

    private function isRangeOperator(string $operator): bool
    {
        return $operator === Operator::LT
            || $operator === Operator::LTE
            || $operator === Operator::GT
            || $operator === Operator::GTE
            || $operator === Operator::BETWEEN;
    }
}

class_alias(FieldRangeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\FieldRangeVisitor');
