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
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Tests\Personalization\Client\Consumer\AbstractConsumerTestCase;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class RevenueDetailsDataFetcherTest extends AbstractConsumerTestCase
{
    private const REPORT_FORMAT = 'xlsx';

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcher */
    private $revenueDetailsDataFetcher;

    /** @var string */
    private $endPointUri;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Value\DateTimeRange */
    private $dateTimeRange;

    /** @var string */
    private $email;

    public function setUp(): void
    {
        parent::setUp();

        $this->endPointUri = 'endpoint.com';
        $this->revenueDetailsDataFetcher = new RevenueDetailsDataFetcher(
            $this->client,
            $this->endPointUri
        );
        $this->customerId = 12345;
        $this->licenseKey = '12345-12345-12345-12345';
        $this->dateTimeRange = new DateTimeRange(
            new DateTimeImmutable('2020-10-10T12:00:00+00:00'),
            new DateTimeImmutable('2020-10-12T12:00:00+00:00')
        );
        $this->email = 'customer@reco.com';
    }

    public function testCreateInstanceRevenueDetailsDataFetcher(): void
    {
        self::assertInstanceOf(
            RevenueDetailsDataFetcherInterface::class,
            $this->revenueDetailsDataFetcher
        );
    }

    /**
     * @dataProvider providerForTestFetchRecommendationEventsSummaryForCommerceType
     */
    public function testFetchRevenueDetailsList(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v4/12345/statistic/added_revenue'),
                [
                    'query' => [
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
                Loader::load(Loader::PERFORMANCE_REVENUE_DETAILS_LIST_NUMERIC_ID_FIXTURE)
            ));

        $fetchedResponse = $this->revenueDetailsDataFetcher->fetchRevenueDetailsList(
            $this->customerId,
            $this->licenseKey,
            $this->dateTimeRange
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
                Loader::load(Loader::PERFORMANCE_REVENUE_DETAILS_LIST_NUMERIC_ID_FIXTURE)
            ),
        ];
    }

    /**
     * @dataProvider providerForTestFetchRevenueReport
     */
    public function testFetchRevenueReport(Response $response): void
    {
        $this->client
            ->expects(self::once())
            ->method('sendRequest')
            ->with(
                'GET',
                new Uri('endpoint.com/api/v4/12345/statistic/added_revenue.xlsx'),
                [
                    'query' => [
                        'from_date_time' => '2020-10-10T12:00:00+00:00',
                        'to_date_time' => '2020-10-12T12:00:00+00:00',
                        'email' => $this->email,
                    ],
                    'headers' => [
                        'Content-Type' => 'application/json',
                    ],
                ]
            )
            ->willReturn(new Response(
                200,
                [],
                Loader::load(Loader::REPORT_REVENUE)
            ));

        $fetchedResponse = $this->revenueDetailsDataFetcher->fetchRevenueReport(
            $this->customerId,
            $this->licenseKey,
            $this->dateTimeRange,
            self::REPORT_FORMAT,
            $this->email
        );

        self::assertEquals(
            $response->getBody()->getContents(),
            $fetchedResponse->getBody()->getContents()
        );
    }

    public function providerForTestFetchRevenueReport(): iterable
    {
        yield [
            new Response(
                200,
                [],
                Loader::load(Loader::REPORT_REVENUE)
            ),
        ];
    }
}

class_alias(RevenueDetailsDataFetcherTest::class, 'Ibexa\Platform\Tests\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherTest');
