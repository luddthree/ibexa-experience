<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Performance;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\SummaryDataFetcherInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceService;
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
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;

final class RecommendationPerformanceServiceTest extends AbstractServiceTestCase
{
    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private EventsDetailsDataFetcherInterface $eventsDetailsDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private EventsSummaryDataFetcherInterface $eventsSummaryDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private PopularityDataFetcherInterface $popularityDataFetcher;

    /** @var \Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    private RecommendationPerformanceServiceInterface $recommendationPerformanceService;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private RevenueDetailsDataFetcherInterface $revenueDetailsListDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\SummaryDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SummaryDataFetcherInterface $summaryDataFetcher;

    private DateTimeRange $dateTimeRange;

    private GranularityDateTimeRange $granularityDateTimeRange;

    private int $customerId;

    private string $licenseKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->contentService = $this->createMock(ContentService::class);
        $this->eventsDetailsDataFetcher = $this->createMock(EventsDetailsDataFetcherInterface::class);
        $this->eventsSummaryDataFetcher = $this->createMock(EventsSummaryDataFetcherInterface::class);
        $this->popularityDataFetcher = $this->createMock(PopularityDataFetcherInterface::class);
        $this->repositoryConfigResolver = $this->createMock(RepositoryConfigResolverInterface::class);
        $this->revenueDetailsListDataFetcher = $this->createMock(RevenueDetailsDataFetcherInterface::class);
        $this->summaryDataFetcher = $this->createMock(SummaryDataFetcherInterface::class);
        $this->recommendationPerformanceService = new RecommendationPerformanceService(
            $this->contentService,
            $this->eventsDetailsDataFetcher,
            $this->eventsSummaryDataFetcher,
            $this->popularityDataFetcher,
            $this->repositoryConfigResolver,
            $this->revenueDetailsListDataFetcher,
            $this->settingService,
            $this->summaryDataFetcher
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
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

    public function testCreateInstanceRecommendationPerformanceService(): void
    {
        self::assertInstanceOf(
            RecommendationPerformanceServiceInterface::class,
            $this->recommendationPerformanceService
        );
    }

    /**
     * @dataProvider providerForTestGetRecommendationSummary
     */
    public function testGetRecommendationSummary(
        RecommendationSummary $recommendationSummary,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->summaryDataFetcher
            ->method('fetchRecommendationSummary')
            ->with(
                $this->customerId,
                $this->licenseKey
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $summary = $this->recommendationPerformanceService->getRecommendationSummary($this->customerId);

        self::assertInstanceOf(
            RecommendationSummary::class,
            $summary
        );
        self::assertEquals(
            $recommendationSummary,
            $summary
        );
    }

    /**
     * @return iterable<array{RecommendationSummary, string}>
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
            Loader::load(Loader::PERFORMANCE_SUMMARY_FIXTURE),
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
            Loader::load(Loader::PERFORMANCE_SUMMARY_CLEAN_FIXTURE),
        ];
    }

    /**
     * @dataProvider providerForTestGetRecommendationEventsSummary
     */
    public function testGetRecommendationEventsSummary(
        RecommendationEventsSummary $recommendationEventsSummary,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->eventsSummaryDataFetcher
            ->method('fetchRecommendationEventsSummary')
            ->with(
                $this->customerId,
                $this->licenseKey,
                new DateTimeRange(
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $eventsSummary = $this->recommendationPerformanceService->getRecommendationEventsSummary(
            $this->customerId,
            $this->dateTimeRange
        );

        self::assertInstanceOf(
            RecommendationEventsSummary::class,
            $eventsSummary
        );
        self::assertEquals(
            $recommendationEventsSummary,
            $eventsSummary
        );
    }

    /**
     * @return iterable<array{RecommendationEventsSummary, string}>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function providerForTestGetRecommendationEventsSummary(): iterable
    {
        yield [
                new RecommendationEventsSummary(
                    new RecommendationCallList(
                        [
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'category_page',
                                    'name' => 'Category page',
                                    'percentage' => '0.0',
                                    'calls' => '84',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'random',
                                    'name' => 'Random',
                                    'percentage' => '39.5',
                                    'calls' => '84539',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'ultimately_bought',
                                    'name' => 'Ultimately bought',
                                    'percentage' => '0.0',
                                    'calls' => '1',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'also_purchased',
                                    'name' => 'Also purchased',
                                    'percentage' => '0.3',
                                    'calls' => '683',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'test',
                                    'name' => 'Test',
                                    'percentage' => '0.0',
                                    'calls' => '25',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'also_purchased2',
                                    'name' => 'Also purchased2',
                                    'percentage' => '0.0',
                                    'calls' => '2',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'landingPage',
                                    'name' => 'landingPage',
                                    'percentage' => '0.1',
                                    'calls' => '274',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'profile_models',
                                    'name' => 'Profile models',
                                    'percentage' => '0.0',
                                    'calls' => '13',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'landing_page',
                                    'name' => 'Landing page',
                                    'percentage' => '0.1',
                                    'calls' => '107',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'ALL_CALLS',
                                    'name' => 'ALL_CALLS',
                                    'percentage' => '100.0',
                                    'calls' => '213877',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'blog',
                                    'name' => 'Blog',
                                    'percentage' => '59.9',
                                    'calls' => '128074',
                                ]
                            ),
                            RecommendationCall::fromArray(
                                [
                                    'id' => 'top_clicked',
                                    'name' => 'Top clicked',
                                    'percentage' => '0.0',
                                    'calls' => '75',
                                ]
                            ),
                        ]
                    ),
                    new ConversionRateList(
                        [
                            ConversionRate::fromArray(
                                [
                                    'id' => 'category_page',
                                    'name' => 'Category page',
                                    'percentage' => '2.4',
                                ]
                            ),
                            ConversionRate::fromArray(
                                [
                                    'id' => 'also_purchased',
                                    'name' => 'Also purchased',
                                    'percentage' => '4.8',
                                ]
                            ),
                            ConversionRate::fromArray(
                                [
                                    'id' => 'also_purchased2',
                                    'name' => 'Also purchased2',
                                    'percentage' => '741000.0',
                                ]
                            ),
                            ConversionRate::fromArray(
                                [
                                    'id' => 'landingPage',
                                    'name' => 'LandingPage',
                                    'percentage' => '3570.1',
                                ]
                            ),
                            ConversionRate::fromArray(
                                [
                                    'id' => 'landing_page',
                                    'name' => 'Landing page',
                                    'percentage' => '2.8',
                                ]
                            ),
                            ConversionRate::fromArray(
                                [
                                    'id' => 'blog',
                                    'name' => 'Blog',
                                    'percentage' => '0.0',
                                ]
                            ),
                            ConversionRate::fromArray(
                                [
                                    'id' => 'ALL_SCENARIOS',
                                    'name' => 'ALL_SCENARIOS',
                                    'percentage' => '19.1',
                                ]
                            ),
                        ]
                    ),
                    new SummaryEventList(
                        [
                            EventSummary::fromArray(
                                [
                                    'name' => 'buy',
                                    'hits' => 10501,
                                ]
                            ),
                            EventSummary::fromArray(
                                [
                                    'name' => 'click',
                                    'hits' => 84196,
                                ]
                            ),
                            EventSummary::fromArray(
                                [
                                    'name' => 'basket',
                                    'hits' => 10615,
                                ]
                            ),
                            EventSummary::fromArray(
                                [
                                    'name' => 'consume',
                                    'hits' => 9,
                                ]
                            ),
                            EventSummary::fromArray(
                                [
                                    'name' => 'total_events',
                                    'hits' => 105321,
                                ]
                            ),
                        ],
                    ),
                    RevenueSummary::fromArray(
                        [
                            'currency' => 'EUR',
                            'items_purchased' => '105',
                            'revenue' => '769500',
                        ]
                    )
                ),
                Loader::load(Loader::PERFORMANCE_EVENTS_SUMMARY_COMMERCE_FIXTURE),
            ];
    }

    /**
     * @dataProvider providerForTestGetRecommendationEventsDetails
     */
    public function testGetRecommendationEventsDetails(
        RecommendationEventsDetails $recommendationEventsDetails,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->eventsDetailsDataFetcher
            ->method('fetchRecommendationEventsDetails')
            ->with(
                $this->customerId,
                $this->licenseKey,
                new GranularityDateTimeRange(
                    'PT1H',
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $eventsDetails = $this->recommendationPerformanceService->getRecommendationEventsDetails(
            $this->customerId,
            $this->granularityDateTimeRange
        );

        self::assertInstanceOf(
            RecommendationEventsDetails::class,
            $eventsDetails
        );
        self::assertEquals(
            $recommendationEventsDetails,
            $eventsDetails
        );
    }

    /**
     * @throws \JsonException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return iterable<array{RecommendationEventsDetails, string}>
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
            $body,
        ];
    }

    /**
     * @dataProvider providerForTestGetRevenueDetailsList
     */
    public function testGetRevenueDetailsList(
        RevenueDetailsList $revenueDetailsList,
        bool $useRemoteId,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->revenueDetailsListDataFetcher
            ->method('fetchRevenueDetailsList')
            ->with(
                $this->customerId,
                $this->licenseKey,
                new DateTimeRange(
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00')
                )
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $this->mockRepositoryConfigResolverUseRemoteId($useRemoteId);

        $responseRevenueDetailsList = $this->recommendationPerformanceService->getRevenueDetailsList(
            $this->customerId,
            $this->dateTimeRange
        );

        self::assertInstanceOf(
            RevenueDetailsList::class,
            $responseRevenueDetailsList
        );
        self::assertEquals(
            $revenueDetailsList,
            $responseRevenueDetailsList
        );
    }

    /**
     * @throws \JsonException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     *
     * @return iterable<array{RevenueDetailsList, bool, string}>
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

    private function mockRepositoryConfigResolverUseRemoteId(bool $useRemoteId): void
    {
        $this->repositoryConfigResolver
            ->expects(self::atLeastOnce())
            ->method('useRemoteId')
            ->willReturn($useRemoteId);
    }

    /**
     * @dataProvider providerForTestGetPopularityList
     */
    public function testGetPopularityList(
        PopularityList $popularityList,
        string $body
    ): void {
        $this->getLicenseKey();
        $popularityDuration = 'VERSION_30DAYS';

        $this->popularityDataFetcher
            ->expects(self::once())
            ->method('fetchPopularityList')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $popularityDuration
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $responsePopularityList = $this->recommendationPerformanceService->getPopularityList(
            $this->customerId,
            $popularityDuration
        );

        self::assertInstanceOf(
            PopularityList::class,
            $responsePopularityList
        );
        self::assertEquals(
            $popularityList,
            $responsePopularityList
        );
    }

    /**
     * @return iterable<array{PopularityList, string}>
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
            $body,
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

class_alias(RecommendationPerformanceServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Service\Performance\RecommendationPerformanceServiceTest');
