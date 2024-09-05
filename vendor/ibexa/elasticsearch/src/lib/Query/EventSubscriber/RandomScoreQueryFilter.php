<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch\Query\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchAll;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Random as RandomSortClause;
use Ibexa\Contracts\Elasticsearch\Query\Event\QueryFilterEvent;
use Ibexa\Contracts\Elasticsearch\Repository\Exceptions\SortClauseConflictException;
use Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query\RandomScore;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class RandomScoreQueryFilter implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            QueryFilterEvent::class => 'onQueryFilterEvent',
        ];
    }

    public function onQueryFilterEvent(QueryFilterEvent $event): void
    {
        $query = $event->getQuery();

        $randomSortClause = $this->findRandomSortClause($query);
        if ($randomSortClause === null) {
            return;
        }

        if (count($query->sortClauses) > 1) {
            throw new SortClauseConflictException('Random Sort Clause cannot be combined with other sort clauses');
        }

        $query->sortClauses = [];
        $query->query = new RandomScore(
            $query->query ?? new MatchAll(),
            $randomSortClause->targetData->seed
        );
    }

    private function findRandomSortClause(Query $query): ?RandomSortClause
    {
        foreach ($query->sortClauses as $sortClause) {
            if ($sortClause instanceof RandomSortClause) {
                return $sortClause;
            }
        }

        return null;
    }
}

class_alias(RandomScoreQueryFilter::class, 'Ibexa\Platform\ElasticSearchEngine\Query\EventSubscriber\RandomScoreQueryFilter');
