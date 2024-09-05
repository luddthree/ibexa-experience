<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Client\Consumer\Performance;

use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Psr\Http\Message\ResponseInterface;

interface RecommendationDetailedReportDataFetcherInterface
{
    public function fetchRecommendationDetailedReport(
        int $customerId,
        string $licenseKey,
        GranularityDateTimeRange $granularityDateTimeRange,
        string $format
    ): ResponseInterface;
}

class_alias(RecommendationDetailedReportDataFetcherInterface::class, 'Ibexa\Platform\Personalization\Client\Consumer\Performance\RecommendationDetailedReportDataFetcherInterface');
