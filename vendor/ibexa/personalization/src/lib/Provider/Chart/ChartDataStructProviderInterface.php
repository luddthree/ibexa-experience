<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Provider\Chart;

use Ibexa\Personalization\Value\Chart\Struct;
use Ibexa\Personalization\Value\Performance\Details\EventList;
use Ibexa\Personalization\Value\Performance\Details\RevenueList;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRateList;
use Ibexa\Personalization\Value\Performance\Summary\EventList as SummaryEventList;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCallList;
use Ibexa\Personalization\Value\Performance\Summary\Revenue;
use Ibexa\Personalization\Value\Scenario\ScenarioList;

/**
 * @internal
 */
interface ChartDataStructProviderInterface
{
    public function provideForRecommendationCallsChart(
        ScenarioList $scenarioList,
        RecommendationCallList $recommendationCallList
    ): Struct;

    public function provideForConversionRateChart(
        ScenarioList $scenarioList,
        ConversionRateList $conversionRateList
    ): Struct;

    public function provideForCollectedEventsChart(
        EventList $eventList,
        SummaryEventList $summaryEventList
    ): Struct;

    public function provideForRevenueChart(
        RevenueList $revenueList,
        Revenue $revenueSummary
    ): Struct;
}

class_alias(ChartDataStructProviderInterface::class, 'Ibexa\Platform\Personalization\Provider\Chart\ChartDataStructProviderInterface');
