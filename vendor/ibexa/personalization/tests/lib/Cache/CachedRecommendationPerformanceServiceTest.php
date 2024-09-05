<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Cache;

use DateTimeImmutable;
use Ibexa\Personalization\Cache\CachedRecommendationPerformanceService;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\Details\Event;
use Ibexa\Personalization\Value\Performance\Details\EventList;
use Ibexa\Personalization\Value\Performance\Details\Revenue;
use Ibexa\Personalization\Value\Performance\Details\RevenueList;
use Ibexa\Personalization\Value\Performance\Popularity\Popularity;
use Ibexa\Personalization\Value\Performance\Popularity\PopularityList;
use Ibexa\Personalization\Value\Performance\RecommendationEventsDetails;
use Ibexa\Personalization\Value\Performance\RecommendationEventsSummary;
use Ibexa\Personalization\Value\Performance\RecommendationSummary;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRate;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRateList;
use Ibexa\Personalization\Value\Performance\Summary\Event as EventSummary;
use Ibexa\Personalization\Value\Performance\Summary\EventList as SummaryEventList;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCall;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCallList;
use Ibexa\Personalization\Value\Performance\Summary\Revenue as RevenueSummary;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class CachedRecommendationPerformanceServiceTest extends AbstractCacheTestCase
{
    private CachedRecommendationPerformanceService $cachedRecommendationPerformanceService;

    /** @var \Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RecommendationPerformanceServiceInterface $innerRecommendationPerformanceService;

    private int $customerId;

    private DateTimeRange $dateTimeRange;

    private GranularityDateTimeRange $granularityDateTimeRange;

    public function setUp(): void
    {
        parent::setUp();

        $this->innerRecommendationPerformanceService = $this->createMock(RecommendationPerformanceServiceInterface::class);
        $this->cachedRecommendationPerformanceService = new CachedRecommendationPerformanceService(
            $this->cache,
            $this->persistenceHandler,
            $this->persistenceLogger,
            $this->cacheIdentifierGenerator,
            $this->cacheIdentifierSanitizer,
            $this->locationPathConverter,
            $this->innerRecommendationPerformanceService
        );
        $this->customerId = 1234;
        $this->dateTimeRange = new DateTimeRange(
            new DateTimeImmutable('2020-10-10 12:00:00'),
            new DateTimeImmutable('2020-10-12 12:00:00')
        );
        $this->granularityDateTimeRange = new GranularityDateTimeRange(
            'PT1H',
            new DateTimeImmutable('2020-10-10 12:00:00'),
            new DateTimeImmutable('2020-10-12 12:00:00')
        );
    }

    public function testCreateInstanceCachedRecommendationPerformanceService(): void
    {
        self::assertInstanceOf(
            RecommendationPerformanceServiceInterface::class,
            $this->cachedRecommendationPerformanceService
        );
    }

    /**
     * @dataProvider providerForTestGetRecommendationSummary
     */
    public function testGetRecommendationSummary(RecommendationSummary $recommendationSummary): void
    {
        $cacheKey = 'ibexa-recommendation-summary-1234';
        $this->cache
            ->method('getItem')
            ->with(
                $cacheKey
            )
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerRecommendationPerformanceService
            ->method('getRecommendationSummary')
            ->with(
                $this->customerId
            )
            ->willReturn($recommendationSummary);

        $fromCache = $this->cachedRecommendationPerformanceService->getRecommendationSummary(1234);

        self::assertInstanceOf(
            RecommendationSummary::class,
            $fromCache
        );
        self::assertEquals(
            $recommendationSummary,
            $fromCache
        );
    }

    /**
     * @return iterable<array{RecommendationSummary}>
     */
    public function providerForTestGetRecommendationSummary(): iterable
    {
        yield [
            RecommendationSummary::fromArray(
                [
                    'totalRecoCalls' => 1000,
                    'currentClickedRecoPercent' => 10,
                    'previousClickedRecoPercent' => 5,
                    'clickedRecoDelta' => 5,
                    'totalImportedItems' => 10000,
                    'lastProductFeed' => '2020-09-10',
                    'eventsCollected' => 2000,
                ]
            ),
        ];
        yield [
            RecommendationSummary::fromArray(
                [
                    'totalRecoCalls' => 0,
                    'currentClickedRecoPercent' => 0,
                    'previousClickedRecoPercent' => 0,
                    'clickedRecoDelta' => 0,
                    'totalImportedItems' => 0,
                    'lastProductFeed' => null,
                    'eventsCollected' => 0,
                ]
            ),
        ];
    }

    /**
     * @dataProvider providerForTestGetRecommendationEventsSummary
     */
    public function testGetRecommendationEventsSummary(RecommendationEventsSummary $recommendationEventsSummary): void
    {
        $cacheKey = 'ibexa-recommendation-events-summary-1234-1602331200-1602504000';
        $this->cache
            ->method('getItem')
            ->with(
                $cacheKey
            )
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerRecommendationPerformanceService
            ->method('getRecommendationEventsSummary')
            ->with(
                $this->customerId,
                new DateTimeRange(
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn($recommendationEventsSummary);

        $fromCache = $this->cachedRecommendationPerformanceService->getRecommendationEventsSummary(
            $this->customerId,
            $this->dateTimeRange
        );

        self::assertInstanceOf(
            RecommendationEventsSummary::class,
            $fromCache
        );
        self::assertEquals(
            $recommendationEventsSummary,
            $fromCache
        );
    }

    /**
     * @return iterable<array{RecommendationEventsSummary}>
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function providerForTestGetRecommendationEventsSummary(): iterable
    {
        yield [
            new RecommendationEventsSummary(
                new RecommendationCallList(
                    [
                        RecommendationCall::fromArray(
                            [
                                'id' => 'also_clicked',
                                'name' => 'also_clicked',
                                'percentage' => '64.2',
                                'calls' => '2112',
                            ]
                        ),
                        RecommendationCall::fromArray(
                            [
                                'id' => 'top_selling',
                                'name' => 'top_selling',
                                'percentage' => '35.8',
                                'calls' => '1176',
                            ]
                        ),
                        RecommendationCall::fromArray(
                            [
                                'id' => 'ALL_CALLS',
                                'name' => 'ALL_CALLS',
                                'percentage' => '100.0',
                                'calls' => '3288',
                            ]
                        ),
                    ]
                ),
                new ConversionRateList(
                    [
                        ConversionRate::fromArray(
                            [
                                'id' => 'also_clicked',
                                'name' => 'Also clicked',
                                'percentage' => '0.0',
                            ]
                        ),
                        ConversionRate::fromArray(
                            [
                                'id' => 'top_selling',
                                'name' => 'Top selling',
                                'percentage' => '0.3',
                            ]
                        ),
                        ConversionRate::fromArray(
                            [
                                'id' => 'ALL_SCENARIOS',
                                'name' => 'ALL_SCENARIOS',
                                'percentage' => '0.2',
                            ]
                        ),
                    ]
                ),
                new SummaryEventList(
                    [
                        EventSummary::fromArray(
                            [
                                'name' => 'BUY',
                                'hits' => 952133,
                            ]
                        ),
                        EventSummary::fromArray(
                            [
                                'name' => 'CLICK',
                                'hits' => 16685175,
                            ]
                        ),
                        EventSummary::fromArray(
                            [
                                'name' => 'BASKET',
                                'hits' => 2216873,
                            ]
                        ),
                        EventSummary::fromArray(
                            [
                                'name' => 'TOTAL_EVENTS',
                                'hits' => 19854181,
                            ]
                        ),
                    ]
                ),
                RevenueSummary::fromArray(
                    [
                        'currency' => 'CZK',
                        'items_purchased' => '24836',
                        'revenue' => '800301600',
                    ]
                )
            ),
        ];
    }

    /**
     * @dataProvider providerForTestGetRecommendationEventsDetails
     */
    public function testGetRecommendationEventsDetails(RecommendationEventsDetails $recommendationEventsDetails): void
    {
        $cacheKey = 'ibexa-recommendation-events-details-1234-PT1H-1602331200-1602504000';
        $this->cache
            ->method('getItem')
            ->with(
                $cacheKey
            )
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerRecommendationPerformanceService
            ->method('getRecommendationEventsDetails')
            ->with(
                $this->customerId,
                new GranularityDateTimeRange(
                    'PT1H',
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn($recommendationEventsDetails);

        $fromCache = $this->cachedRecommendationPerformanceService->getRecommendationEventsDetails(
            $this->customerId,
            $this->granularityDateTimeRange
        );

        self::assertInstanceOf(
            RecommendationEventsDetails::class,
            $fromCache
        );
        self::assertEquals(
            $recommendationEventsDetails,
            $fromCache
        );
    }

    /**
     * @throws \JsonException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return iterable<array{RecommendationEventsDetails}>
     */
    public function providerForTestGetRecommendationEventsDetails(): iterable
    {
        $body = Loader::load(Loader::PERFORMANCE_EVENTS_DETAILS_FIXTURE);
        /** @var array{
         *      'revenueList': array<\Ibexa\Personalization\Value\Performance\Details\Revenue>,
         *      'eventList': array<\Ibexa\Personalization\Value\Performance\Details\Event>,
         * } $events
         */
        $events = [];

        $eventsDetails = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        foreach ($eventsDetails as $event) {
            $events['revenueList'][] = $this->getRevenue($event);
            $events['eventList'][] = $this->getEvent($event);
        }

        yield [
            new RecommendationEventsDetails(
                new RevenueList($events['revenueList']),
                new EventList($events['eventList']),
            ),
        ];
    }

    /**
     * @dataProvider providerForTestGetRevenueDetailsList
     */
    public function testGetRevenueDetailsList(RevenueDetailsList $revenueDetailsList): void
    {
        $cacheKey = 'ibexa-revenue-details-list-1234-1602331200-1602504000';
        $this->cache
            ->method('getItem')
            ->with(
                $cacheKey
            )
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerRecommendationPerformanceService
            ->method('getRevenueDetailsList')
            ->with(
                $this->customerId,
                new DateTimeRange(
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn($revenueDetailsList);

        $fromCache = $this->cachedRecommendationPerformanceService->getRevenueDetailsList(
            $this->customerId,
            $this->dateTimeRange
        );

        self::assertInstanceOf(
            RevenueDetailsList::class,
            $fromCache
        );
        self::assertEquals(
            $revenueDetailsList,
            $fromCache
        );
    }

    /**
     * @return iterable<array{
     *  \Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList,
     *  bool,
     *  string
     * }>
     *
     * @throws \JsonException
     */
    public function providerForTestGetRevenueDetailsList(): iterable
    {
        $revenueDetailsListWithNumericIdsBody = Loader::load(
            Loader::PERFORMANCE_REVENUE_DETAILS_LIST_NUMERIC_ID_FIXTURE
        );
        $revenueDetailsListWithAlphaNumericIdsBody = Loader::load(
            Loader::PERFORMANCE_REVENUE_DETAILS_LIST_ALPHANUMERIC_ID_FIXTURE
        );

        yield [
            $this->getRevenueDetailsList(
                $revenueDetailsListWithNumericIdsBody,
                [
                    932 => 'Product 1',
                    896 => 'Product 2',
                    915 => 'Product 3',
                ]
            ),
            false,
            $revenueDetailsListWithNumericIdsBody,
        ];

        yield [
            $this->getRevenueDetailsList(
                $revenueDetailsListWithAlphaNumericIdsBody,
                [
                    'd8038e2af9770f108b763c368c05fd02' => 'Product 1',
                    '637d58bfddf164627bdfd265733280a0' => 'Product 2',
                    '40faa822edc579b02c25f6bb7beec3ad' => 'Product 3',
                ]
            ),
            true,
            $revenueDetailsListWithAlphaNumericIdsBody,
        ];
    }

    /**
     * @param array<int|string, string> $itemIdsMap
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \JsonException
     */
    private function getRevenueDetailsList(string $body, array $itemIdsMap): RevenueDetailsList
    {
        $revenueDetails = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $revenueDetailsItems = [];
        foreach ($revenueDetails as $revenue) {
            if (!isset($revenue['title'])) {
                $revenue['title'] = $itemIdsMap[$revenue['item']['id']];
            }

            $revenueDetailsItems[] = RevenueDetails::fromArray($revenue);
        }

        return new RevenueDetailsList($revenueDetailsItems);
    }

    /**
     * @dataProvider providerForTestGetPopularityList
     */
    public function testGetPopularityList(PopularityList $popularityList): void
    {
        $popularityDuration = 'VERSION_30DAYS';
        $cacheKey = 'ibexa-popularity-list-1234-VERSION__30DAYS';
        $this->cache
            ->expects(self::atLeastOnce())
            ->method('getItem')
            ->with(
                $cacheKey
            )
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerRecommendationPerformanceService
            ->method('getPopularityList')
            ->with(
                $this->customerId,
                $popularityDuration
            )
            ->willReturn($popularityList);

        $fromCache = $this->cachedRecommendationPerformanceService->getPopularityList(
            $this->customerId,
            $popularityDuration
        );

        self::assertEquals(
            $popularityList,
            $fromCache
        );
    }

    /**
     * @return iterable<array{PopularityList}>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \JsonException
     */
    public function providerForTestGetPopularityList(): iterable
    {
        $body = Loader::load(Loader::PERFORMANCE_POPULARITY_LIST_FIXTURE);
        $popularityItems = [];

        $popularityResponse = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        foreach ($popularityResponse as $popularity) {
            $popularityItems[] = Popularity::fromArray($popularity);
        }

        yield [
            new PopularityList($popularityItems),
        ];
    }

    /**
     * @param array<string> $event
     */
    private function getRevenue(array $event): Revenue
    {
        return Revenue::fromArray([
            'revenue' => $event['revenue'],
            'purchased_recommended' => $event['purchasedRecommended'],
            'timespan_begin' => $event['timespanBegin'],
            'timespan_duration' => $event['timespanDuration'],
        ]);
    }

    /**
     * @param array<string> $event
     */
    private function getEvent(array $event): Event
    {
        return Event::fromArray([
            Event::CLICK => $event['clickEvents'],
            Event::PURCHASE => $event['purchaseEvents'],
            Event::CLICKED_RECOMMENDED => $event['clickedRecommended'],
            Event::CONSUME => $event['consumeEvents'],
            Event::RATE => $event['rateEvents'],
            Event::RENDERED => $event['renderedEvents'],
            Event::BLACKLIST => $event['blacklistEvents'],
            Event::BASKET => $event['basketEvents'],
            Event::PURCHASED_RECOMMENDED => $event['purchasedRecommended'],
            Event::TIMESPAN_BEGIN => $event['timespanBegin'],
            Event::TIMESPAN_DURATION => $event['timespanDuration'],
        ]);
    }
}

class_alias(CachedRecommendationPerformanceServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Cache\CachedRecommendationPerformanceServiceTest');
