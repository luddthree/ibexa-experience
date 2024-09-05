<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Service\Report;

use DateTimeImmutable;
use GuzzleHttp\Psr7\Response;
use Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface;
use Ibexa\Personalization\Exception\FailedToFetchReportException;
use Ibexa\Personalization\Exception\UnsupportedFormatException;
use Ibexa\Personalization\Service\Report\ReportService;
use Ibexa\Personalization\Service\Report\ReportServiceInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\RecommendationDetailedReport;
use Ibexa\Personalization\Value\Performance\Revenue\Report as RevenueReport;
use Ibexa\Tests\Personalization\Fixture\Loader;
use Ibexa\Tests\Personalization\Service\AbstractServiceTestCase;

final class ReportServiceTest extends AbstractServiceTestCase
{
    private const REPORT_FORMAT = 'xlsx';
    private const UNSUPPORTED_REPORT_FORMAT = 'xml';

    /** @var \Ibexa\Personalization\Service\Report\ReportService */
    private $reportService;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $recommendationDetailedReportDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    private $revenueDetailsDataFetcher;

    /** @var int */
    private $customerId;

    /** @var string */
    private $licenseKey;

    /** @var \Ibexa\Personalization\Value\DateTimeRange */
    private $dateTimeRange;

    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $granularityDateTimeRange;

    /** @var string */
    private $email;

    public function setUp(): void
    {
        parent::setUp();

        $this->recommendationDetailedReportDataFetcher = $this->createMock(
            RecommendationDetailedReportDataFetcherInterface::class
        );
        $this->revenueDetailsDataFetcher = $this->createMock(RevenueDetailsDataFetcherInterface::class);
        $this->reportService = new ReportService(
            $this->settingService,
            $this->recommendationDetailedReportDataFetcher,
            $this->revenueDetailsDataFetcher
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
        $this->email = 'customer@reco.com';
    }

    public function testCreateInstanceReportService(): void
    {
        self::assertInstanceOf(
            ReportServiceInterface::class,
            $this->reportService
        );
    }

    /**
     * @dataProvider providerForTestGetRecommendationDetailedReport
     */
    public function testGetRecommendationDetailedReport(
        RecommendationDetailedReport $recommendationDetailedReport,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->recommendationDetailedReportDataFetcher
            ->expects(self::once())
            ->method('fetchRecommendationDetailedReport')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $this->granularityDateTimeRange
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $report = $this->reportService->getRecommendationDetailedReport(
            $this->customerId,
            $this->granularityDateTimeRange,
            self::REPORT_FORMAT
        );

        self::assertInstanceOf(
            RecommendationDetailedReport::class,
            $report
        );
        self::assertEquals(
            $recommendationDetailedReport,
            $report
        );
    }

    public function providerForTestGetRecommendationDetailedReport(): iterable
    {
        $body = Loader::load(Loader::REPORT_RECOMMENDATION_DETAILED);

        yield [
            new RecommendationDetailedReport(
                'Recommendation detailed report 12345 2020-10-10 to 2020-10-12',
                $body
            ),
            $body,
        ];
    }

    public function testRecommendationDetailedReportThrowUnsupportedFormatException(): void
    {
        self::expectException(UnsupportedFormatException::class);
        self::expectExceptionMessage('Given format: xml is unsupported. Allowed formats: xlsx');

        $this->reportService->getRecommendationDetailedReport(
            $this->customerId,
            $this->granularityDateTimeRange,
            self::UNSUPPORTED_REPORT_FORMAT
        );
    }

    /**
     * @dataProvider providerForTestGetRevenueReport
     */
    public function testGetRevenueReport(
        RevenueReport $revenueReport,
        string $body
    ): void {
        $this->getLicenseKey();

        $this->revenueDetailsDataFetcher
            ->expects(self::once())
            ->method('fetchRevenueReport')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $this->dateTimeRange
            )
            ->willReturn(
                new Response(
                    200,
                    [],
                    $body
                )
            );

        $report = $this->reportService->getRevenueReport(
            $this->customerId,
            $this->dateTimeRange,
            self::REPORT_FORMAT
        );

        self::assertInstanceOf(
            RevenueReport::class,
            $report
        );
        self::assertEquals(
            $revenueReport,
            $report
        );
    }

    public function providerForTestGetRevenueReport(): iterable
    {
        $body = Loader::load(Loader::REPORT_REVENUE);

        yield [
            new RevenueReport(
                false,
                'Revenue report 2020-10-10 to 2020-10-12',
                $body
            ),
            $body,
        ];
    }

    public function testRevenueReportThrowUnsupportedFormatException(): void
    {
        self::expectException(UnsupportedFormatException::class);
        self::expectExceptionMessage('Given format: xml is unsupported. Allowed formats: xlsx');

        $this->reportService->getRevenueReport(
            $this->customerId,
            $this->dateTimeRange,
            self::UNSUPPORTED_REPORT_FORMAT
        );
    }

    public function testThrowFailedToFetchRevenueReportException(): void
    {
        self::expectException(FailedToFetchReportException::class);
        self::expectExceptionMessage('Failed to fetch revenue report');

        $this->getLicenseKey();

        $this->revenueDetailsDataFetcher
            ->expects(self::once())
            ->method('fetchRevenueReport')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $this->dateTimeRange,
                self::REPORT_FORMAT,
                $this->email
            )
            ->willReturn(
                new Response(
                    404
                )
            );

        $this->reportService->getRevenueReport(
            $this->customerId,
            $this->dateTimeRange,
            self::REPORT_FORMAT,
            $this->email
        );
    }

    public function testThrowFailedToFetchRecommendationDetailedReportException(): void
    {
        self::expectException(FailedToFetchReportException::class);
        self::expectExceptionMessage('Failed to fetch recommendation detailed report');

        $this->getLicenseKey();

        $this->recommendationDetailedReportDataFetcher
            ->expects(self::once())
            ->method('fetchRecommendationDetailedReport')
            ->with(
                $this->customerId,
                $this->licenseKey,
                $this->granularityDateTimeRange,
                self::REPORT_FORMAT,
            )
            ->willReturn(
                new Response(
                    204
                )
            );

        $this->reportService->getRecommendationDetailedReport(
            $this->customerId,
            $this->granularityDateTimeRange,
            self::REPORT_FORMAT
        );
    }
}

class_alias(ReportServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Service\Report\ReportServiceTest');
