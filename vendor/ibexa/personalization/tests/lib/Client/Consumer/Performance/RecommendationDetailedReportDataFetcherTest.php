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
use Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class RecommendationDetailedReportDataFetcherTest extends AbstractConsumerTestCase
{
    private const REPORT_FORMAT = 'xlsx';

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcher */
    private $recommendationDetailedReportDataFetcher;

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
        $this->recommendationDetailedReportDataFetcher = new RecommendationDetailedReportDataFetcher(
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

    public function testCreateInstanceRecommendationDetailedReportDataFetcher(): void
    {
        self::assertInstanceOf(
            RecommendationDetailedReportDataFetcherInterface::class,
            $this->recommendationDetailedReportDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchRecommendationDetailedReport
     */
    public function testFetchRecommendationDetailedReport(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v3/12345/revenue/statistic.' . self::REPORT_FORMAT),
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
                Loader::load(Loader::REPORT_RECOMMENDATION_DETAILED)
            ));

        $fetchedResponse = $this->recommendationDetailedReportDataFetcher->fetchRecommendationDetailedReport(
            $this->customerId,
            $this->licenseKey,
            $this->granularityDateTimeRange,
            self::REPORT_FORMAT
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchRecommendationDetailedReport(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::REPORT_RECOMMENDATION_DETAILED)
            ),
        ];
    }
}

class_alias(RecommendationDetailedReportDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherTest');
