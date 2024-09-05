<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Performance;

use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\Popularity\PopularityList;
use Ibexa\Personalization\Value\Performance\RecommendationEventsDetails;
use Ibexa\Personalization\Value\Performance\RecommendationEventsSummary;
use Ibexa\Personalization\Value\Performance\RecommendationSummary;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList;

/**
 * @internal
 */
interface RecommendationPerformanceServiceInterface
{
    public function getRecommendationSummary(
        int $customerId,
        ?string $duration = null
    ): RecommendationSummary;

    public function getRecommendationEventsSummary(
        int $customerId,
        DateTimeRange $dateTimeRange
    ): RecommendationEventsSummary;

    public function getRecommendationEventsDetails(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): RecommendationEventsDetails;

    public function getRevenueDetailsList(
        int $customerId,
        DateTimeRange $dateTimeRange
    ): RevenueDetailsList;

    public function getPopularityList(
        int $customerId,
        string $duration
    ): PopularityList;
}

class_alias(RecommendationPerformanceServiceInterface::class, 'Ibexa\Platform\Personalization\Service\Performance\RecommendationPerformanceServiceInterface');
