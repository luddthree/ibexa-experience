<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Client\Consumer\Performance;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Ibexa\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcherInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class EventsSummaryDataFetcherTest extends AbstractConsumerTestCase
{
    private const DURATION_DAILY = 'H24';
    private const DURATION_WEEKLY = 'WEEK';

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcher */
    private $eventsSummaryDataFetcher;

    /** @var string */
    private $endPointUri;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Value\DateTimeRange */
    private $dateTimeRange;

    /** @var \Ibexa\Personalization\Value\DateTimeRange */
    private $dateTimeRangeWithDurationMoreThan7Days;

    public function setUp(): void
    {
        parent::setUp();

        $this->endPointUri = 'endpoint.com';
        $this->eventsSummaryDataFetcher = new EventsSummaryDataFetcher(
            $this->client,
            $this->endPointUri
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
        $this->dateTimeRange = new DateTimeRange(
            new DateTimeImmutable('2020-10-12T12:00:00+00:00'),
            new DateTimeImmutable('2020-10-12T12:00:00+00:00')
        );
        $this->dateTimeRangeWithDurationMoreThan7Days = new DateTimeRange(
            new DateTimeImmutable('2020-10-12T12:00:00+00:00'),
            new DateTimeImmutable('2020-10-20T12:00:00+00:00')
        );
    }

    public function testCreateInstanceEventsSummaryDataFetcher(): void
    {
        self::assertInstanceOf(
            EventsSummaryDataFetcherInterface::class,
            $this->eventsSummaryDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchRecommendationEventsSummaryForCommerceType
     */
    public function testFetchRecommendationEventsSummaryForCommerceType(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v5/12345/recommendation/performance/events-summary'),
                [
                    'query' => [
                        'fromDate' => '2020-10-12T12:00:00+00:00',
                        'toDate' => '2020-10-20T12:00:00+00:00',
                        'duration' => self::DURATION_WEEKLY,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_EVENTS_SUMMARY_COMMERCE_FIXTURE)
            ));

        $fetchedResponse = $this->eventsSummaryDataFetcher->fetchRecommendationEventsSummary(
            $this->customerId,
            $this->licenseKey,
            $this->dateTimeRangeWithDurationMoreThan7Days
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchRecommendationEventsSummaryForCommerceType(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_EVENTS_SUMMARY_COMMERCE_FIXTURE)
            ),
        ];
    }

    /**
     * @dataProvider providerForTestFetchRecommendationEventsSummaryForPublisherType
     */
    public function testFetchRecommendationEventsSummaryForPublisherType(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v5/12345/recommendation/performance/events-summary'),
                [
                    'query' => [
                        'fromDate' => '2020-10-12T12:00:00+00:00',
                        'toDate' => '2020-10-12T12:00:00+00:00',
                        'duration' => self::DURATION_DAILY,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_EVENTS_SUMMARY_PUBLISHER_FIXTURE)
            ));

        $fetchedResponse = $this->eventsSummaryDataFetcher->fetchRecommendationEventsSummary(
            $this->customerId,
            $this->licenseKey,
            $this->dateTimeRange
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchRecommendationEventsSummaryForPublisherType(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_EVENTS_SUMMARY_PUBLISHER_FIXTURE)
            ),
        ];
    }
}

class_alias(EventsSummaryDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcherTest');
