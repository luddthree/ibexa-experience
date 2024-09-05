<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\CustomField;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Elasticsearch\Query\CriterionVisitor;
use Ibexa\Contracts\Elasticsearch\Query\LanguageFilter;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\TermsQuery;

final class CustomFieldInVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        if ($criterion instanceof CustomField) {
            return $criterion->operator === Operator::EQ
                || $criterion->operator === Operator::IN
                || $criterion->operator === Operator::CONTAINS;
        }

        return false;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        $query = new BoolQuery();

        $values = (array)$criterion->value;
        foreach ($values as $value) {
            $query->addShould(new TermsQuery($criterion->target, (array)$value));
        }

        return $query->toArray();
    }
}

class_alias(CustomFieldInVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\CustomFieldInVisitor');
