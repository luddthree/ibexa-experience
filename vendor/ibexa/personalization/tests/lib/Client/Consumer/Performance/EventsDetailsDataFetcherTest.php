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
use Ibexa\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcherInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class EventsDetailsDataFetcherTest extends AbstractConsumerTestCase
{
    /** @var \Ibexa\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcher */
    private $eventsDetailsDataFetcher;

    /** @var string */
    private $endPointUri;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $granularityDateTimeRange;

    public function setUp(): void
    {
        parent::setUp();

        $this->endPointUri = 'endpoint.com';
        $this->eventsDetailsDataFetcher = new EventsDetailsDataFetcher(
            $this->client,
            $this->endPointUri
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
        $this->granularityDateTimeRange = new GranularityDateTimeRange(
            'PT1H',
            new DateTimeImmutable('2020-10-10T12:00:00+00:00'),
            new DateTimeImmutable('2020-10-12T12:00:00+00:00')
        );
    }

    public function testCreateInstanceEventsDetailsDataFetcher(): void
    {
        self::assertInstanceOf(
            EventsDetailsDataFetcherInterface::class,
            $this->eventsDetailsDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchRecommendationEventsDetails
     */
    public function testFetchRecommendationEventsDetails(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v4/12345/statistic/summary/REVENUE,RECOS,EVENTS'),
                [
                    'query' => [
                        'granularity' => 'PT1H',
                        'from_date_time' => '2020-10-10T12:00:00+00:00',
                        'to_date_time' => '2020-10-12T12:00:00+00:00',
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_EVENTS_DETAILS_FIXTURE)
            ));

        $fetchedResponse = $this->eventsDetailsDataFetcher->fetchRecommendationEventsDetails(
            $this->customerId,
            $this->licenseKey,
            $this->granularityDateTimeRange
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchRecommendationEventsDetails(): iterable
    {
        yield [
            $response = new Response(
                200,
                [],
                Loader::load(Loader::PERFORMANCE_EVENTS_DETAILS_FIXTURE)
            ),
        ];
    }
}

class_alias(EventsDetailsDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcherTest');
