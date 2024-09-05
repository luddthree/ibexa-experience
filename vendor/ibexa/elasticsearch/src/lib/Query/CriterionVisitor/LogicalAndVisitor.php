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

final class LogicalAndVisitor implements CriterionVisitor
{
    public function supports(Criterion $criterion, LanguageFilter $languageFilter): bool
    {
        return $criterion instanceof Criterion\LogicalAnd;
    }

    public function visit(CriterionVisitor $dispatcher, Criterion $criterion, LanguageFilter $languageFilter): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd $criterion */
        if (empty($criterion->criteria)) {
            throw new RuntimeException('Invalid aggregation in LogicalAnd criterion.');
        }

        if (count($criterion->criteria) === 1) {
            return $dispatcher->visit($dispatcher, $criterion->criteria[0], $languageFilter);
        }

        $qb = new BoolQuery();
        foreach ($criterion->criteria as $criterion) {
            $qb->addMust(new RawQuery($dispatcher->visit($dispatcher, $criterion, $languageFilter)));
        }

        return $qb->toArray();
    }
}

class_alias(LogicalAndVisitor::class, 'Ibexa\Platform\ElasticSearchEngine\Query\CriterionVisitor\LogicalAndVisitor');
