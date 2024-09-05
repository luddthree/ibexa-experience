<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Field;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Core\Search\Common\FieldNameResolver;
use Ibexa\Core\Search\Common\FieldValueMapper;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermQuery;
use Ibexa\Elasticsearch\Query\CriterionVisitor\Iterator\FieldCriterionTargetIterator;

final class FieldInVisitor implements CriterionVisitor
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
        if ($criterion instanceof Field) {
            switch ($criterion->operator) {
                case Criterion\Operator::EQ:
                case Criterion\Operator::IN:
                case Criterion\Operator::CONTAINS:
                    return true;
                default:
                    return false;
            }
        }

        return false;
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
            $query->addShould(new TermQuery($name, $value));
        }

        return $query->toArray();
    }
}

class_alias(FieldInVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\FieldInVisitor');
