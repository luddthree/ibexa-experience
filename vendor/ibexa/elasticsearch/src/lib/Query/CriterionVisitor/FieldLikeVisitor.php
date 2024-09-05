<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Field;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Core\Search\Common\FieldValueMapper;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\WildcardQuery;
use Ibexa\Elasticsearch\Query\CriterionVisitor\Iterator\FieldCriterionTargetIterator;

final class FieldLikeVisitor implements CriterionVisitor
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
        return $criterion instanceof Field && $criterion->operator === Operator::LIKE;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $iterator = new FieldCriterionTargetIterator(
            $this->fieldNameResolver,
            $this->fieldValueMapper,
            $criterion
        );

        $query = new BoolQuery();
        foreach ($iterator as $field => $value) {
            if ($this->supportsLikeWildcard($value) && $this->containsWildcard((string)$value)) {
                // FIXME: Fails on non strings / keywords fields
                $query->addShould(new WildcardQuery($field, (string)$value));
            } else {
                $query->addShould(new TermQuery($field, $value));
            }
        }

        return $query->toArray();
    }

    private function supportsLikeWildcard($value): bool
    {
        return !is_numeric($value) && !is_bool($value);
    }

    private function containsWildcard(string $value): bool
    {
        return strpos($value, '*') !== false;
    }
}

class_alias(FieldLikeVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\FieldLikeVisitor');
