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
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\BoolQuery;
use Ibexa\Elasticsearch\ElasticSearch\QueryDSL\RawQuery;
use RuntimeException;

final class LogicalNotVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\LogicalNot;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        if (count($criterion->criteria) !== 1) {
            throw new RuntimeException('Invalid aggregation in LogicalNot criterion.');
        }

        $qb = new BoolQuery();
        $qb->addMustNot(new RawQuery($dispatcher->visit($dispatcher, $criterion->criteria[0], $languageFilter)));

        return $qb->toArray();
    }
}

class_alias(LogicalNotVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\LogicalNotVisitor');
