<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Report;

use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\RecommendationDetailedReport;
use Ibexa\Personalization\Value\Performance\Revenue\Report;

interface ReportServiceInterface
{
    public function getRecommendationDetailedReport(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange,
        string $format
    ): RecommendationDetailedReport;

    public function getRevenueReport(
        int $customerId,
        DateTimeRange $dateTimeRange,
        string $format,
        ?string $email = null
    ): Report;
}

class_alias(ReportServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Report\ReportServiceInterface');
