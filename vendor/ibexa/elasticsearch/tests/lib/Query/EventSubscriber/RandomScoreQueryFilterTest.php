<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Elasticsearch\Query\EventSubscriber;

use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\MatchAll;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\ContentName;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Random;
use Ibexa\Contracts\Elasticsearch\Query\Event\QueryFilterEvent;
use Ibexa\Contracts\Elasticsearch\Repository\Exceptions\SortClauseConflictException;
use Ibexa\Contracts\Elasticsearch\Repository\Values\Content\Query\RandomScore;
use Ibexa\Elasticsearch\Query\EventSubscriber\RandomScoreQueryFilter;
use Ibexa\Tests\Elasticsearch\Query\Utils\MockUtils;
use PHPUnit\Framework\TestCase;

final class RandomScoreQueryFilterTest extends TestCase
{
    private const EXAMPLE_SEED = 0xFF;

    /**
     * @dataProvider dataProviderForTestOnQueryFilterEvent
     */
    public function testOnQueryFilterEvent(Query $sourceQuery, Query $targetQuery): void
    {
        $event = new QueryFilterEvent($sourceQuery, MockUtils::createEmptyLanguageFilter());

        $filter = new RandomScoreQueryFilter();
        $filter->onQueryFilterEvent($event);

        $this->assertEquals($targetQuery, $event->getQuery());
    }

    public function dataProviderForTestOnQueryFilterEvent(): iterable
    {
        yield 'without criteria' => [
            $this->createQuery(null, [new Random()]),
            $this->createQuery(new RandomScore(new MatchAll()), []),
        ];

        yield 'with criteria' => [
            $this->createQuery(
                new ContentTypeIdentifier('article'),
                [
                    new Random(self::EXAMPLE_SEED),
                ],
            ),
            $this->createQuery(
                new RandomScore(
                    new ContentTypeIdentifier('article'),
                    self::EXAMPLE_SEED
                ),
                []
            ),
        ];
    }

    public function testOnQueryFilterEventWithCombinedRandomSortClause(): void
    {
        $this->expectException(SortClauseConflictException::class);
        $this->expectErrorMessage('Random Sort Clause cannot be combined with other sort clauses');

        $query = new Query();
        $query->sortClauses[] = new ContentName();
        $query->sortClauses[] = new Random();

        $filter = new RandomScoreQueryFilter();
        $filter->onQueryFilterEvent(new QueryFilterEvent($query, MockUtils::createEmptyLanguageFilter()));
    }

    private function createQuery(?Criterion $criterion = null, array $sortClauses = []): Query
    {
        $query = new Query();
        $query->query = $criterion;
        $query->sortClauses = $sortClauses;

        return $query;
    }
}

class_alias(RandomScoreQueryFilterTest::class, 'Ibexa\Platform\ElasticSearchEngine\Tests\Query\EventSubscriber\RandomScoreQueryFilterTest');
