<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Report;

use Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcher;
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface;
use Ibexa\Personalization\Exception\FailedToFetchReportException;
use Ibexa\Personalization\Exception\UnsupportedFormatException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\RecommendationDetailedReport;
use Ibexa\Personalization\Value\Performance\Revenue\Report as RevenueReport;

final class ReportService implements ReportServiceInterface
{
    public const FORMAT_XLSX = 'xlsx';
    private const RECOMMENDATION_DETAILED_REPORT_NAME = 'recommendation detailed';
    private const REVENUE_REPORT_NAME = 'revenue';
    private const RECOMMENDATION_DETAILED_REPORT_FILE_NAME = 'Recommendation detailed report %d %s to %s';
    private const REVENUE_REPORT_FILE_NAME = 'Revenue report %s to %s';

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherInterface */
    private $recommendationDetailedReportDataFetcher;

    /** @var \Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface */
    private $revenueDetailsDataFetcher;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface */
    private $settingService;

    public function __construct(
        SettingServiceInterface $settingService,
        RecommendationDetailedReportDataFetcherInterface $recommendationDetailedReportDataFetcher,
        RevenueDetailsDataFetcherInterface $revenueDetailsDataFetcher
    ) {
        $this->recommendationDetailedReportDataFetcher = $recommendationDetailedReportDataFetcher;
        $this->revenueDetailsDataFetcher = $revenueDetailsDataFetcher;
        $this->settingService = $settingService;
    }

    public function getRecommendationDetailedReport(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange,
        string $format
    ): RecommendationDetailedReport {
        $this->performFormatCheck($format, RecommendationDetailedReportDataFetcher::ALLOWED_FORMATS);

        $response = $this->recommendationDetailedReportDataFetcher->fetchRecommendationDetailedReport(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $granularityDateTimeRange,
            $format
        );

        if ($response->getStatusCode() !== 200) {
            throw new FailedToFetchReportException(self::RECOMMENDATION_DETAILED_REPORT_NAME);
        }

        $name = sprintf(
            self::RECOMMENDATION_DETAILED_REPORT_FILE_NAME,
            $customerId,
            $granularityDateTimeRange->getFromDate()->format('Y-m-d'),
            $granularityDateTimeRange->getToDate()->format('Y-m-d')
        );

        return new RecommendationDetailedReport($name, $response->getBody()->getContents());
    }

    public function getRevenueReport(
        int $customerId,
        DateTimeRange $dateTimeRange,
        string $format,
        ?string $email = null
    ): RevenueReport {
        $this->performFormatCheck($format, RevenueDetailsDataFetcher::ALLOWED_FORMATS);

        $response = $this->revenueDetailsDataFetcher->fetchRevenueReport(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $dateTimeRange,
            $format,
            $email
        );

        if ($response->getStatusCode() === 200) {
            $name = sprintf(
                self::REVENUE_REPORT_FILE_NAME,
                $dateTimeRange->getFromDate()->format('Y-m-d'),
                $dateTimeRange->getToDate()->format('Y-m-d')
            );

            return new RevenueReport(
                false,
                $name,
                $response->getBody()->getContents()
            );
        }

        if ($response->getStatusCode() === 202) {
            return new RevenueReport(true);
        }

        throw new FailedToFetchReportException(self::REVENUE_REPORT_NAME);
    }

    private function performFormatCheck(string $format, array $allowedFormats): void
    {
        if (false === in_array($format, $allowedFormats, true)) {
            throw new UnsupportedFormatException($format, $allowedFormats);
        }
    }
}

class_alias(ReportService::class, 'Ibexa\Platform\Personalization\Service\Report\ReportService');
