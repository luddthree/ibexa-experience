<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Chart;

use Ibexa\Personalization\Factory\Chart\ChartFactoryInterface;
use Ibexa\Personalization\Permission\CustomerTypeCheckerInterface;
use Ibexa\Personalization\Provider\Chart\ChartDataStructProviderInterface;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Chart\Chart;
use Ibexa\Personalization\Value\Chart\ChartParameters;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\Performance\Details\EventList;
use Ibexa\Personalization\Value\Performance\Details\RevenueList;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRateList;
use Ibexa\Personalization\Value\Performance\Summary\EventList as SummaryEventList;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCallList;
use Ibexa\Personalization\Value\Performance\Summary\Revenue;
use Ibexa\Personalization\Value\Scenario\ScenarioList;

final class ChartService implements ChartServiceInterface
{
    private CustomerTypeCheckerInterface $customerTypeChecker;

    private RecommendationPerformanceServiceInterface $recommendationPerformanceService;

    private ScenarioServiceInterface $scenarioService;

    private ChartFactoryInterface $chartFactory;

    private ChartDataStructProviderInterface $chartDataStructProvider;

    public function __construct(
        CustomerTypeCheckerInterface $customerTypeChecker,
        RecommendationPerformanceServiceInterface $recommendationPerformanceService,
        ScenarioServiceInterface $scenarioService,
        ChartFactoryInterface $chartFactory,
        ChartDataStructProviderInterface $chartDataStructProvider
    ) {
        $this->customerTypeChecker = $customerTypeChecker;
        $this->recommendationPerformanceService = $recommendationPerformanceService;
        $this->scenarioService = $scenarioService;
        $this->chartFactory = $chartFactory;
        $this->chartDataStructProvider = $chartDataStructProvider;
    }

    public function getCharts(int $customerId, ChartParameters $parameters): array
    {
        $dateTimeRange = new DateTimeRange(
            $parameters->getDateTimeRange()->getFromDate(),
            $parameters->getDateTimeRange()->getToDate()
        );
        $recommendationEventsSummary = $this->recommendationPerformanceService->getRecommendationEventsSummary(
            $customerId,
            $dateTimeRange
        );
        $recommendationEventsDetails = $this->recommendationPerformanceService->getRecommendationEventsDetails(
            $customerId,
            $parameters->getDateTimeRange()
        );
        $scenarioList = $this->scenarioService->getCalledScenarios(
            $customerId,
            $parameters->getDateTimeRange()
        );

        $charts = [
            'recommendation_calls' => $this->getRecommendationCallsChart(
                $scenarioList,
                $recommendationEventsSummary->getRecommendationCallListSummary()
            ),
            'conversion_rate' => $this->getConversionRateChart(
                $scenarioList,
                $recommendationEventsSummary->getConversionSummary()
            ),
            'collected_events' => $this->getCollectedEventsChart(
                $recommendationEventsDetails->getEventList(),
                $recommendationEventsSummary->getEventList()
            ),
        ];

        if (
            $this->customerTypeChecker->isCommerce($customerId)
            && null !== $recommendationEventsSummary->getRevenueSummary()
        ) {
            $charts['revenue'] = $this->getRevenueChart(
                $recommendationEventsDetails->getRevenueList(),
                $recommendationEventsSummary->getRevenueSummary()
            );
        }

        return $charts;
    }

    private function getRecommendationCallsChart(
        ScenarioList $scenarioList,
        RecommendationCallList $recommendationCallList
    ): Chart {
        return $this->chartFactory->create(
            $this->chartDataStructProvider->provideForRecommendationCallsChart($scenarioList, $recommendationCallList)
        );
    }

    private function getConversionRateChart(
        ScenarioList $scenarioList,
        ConversionRateList $conversionRateList
    ): Chart {
        return $this->chartFactory->create(
            $this->chartDataStructProvider->provideForConversionRateChart($scenarioList, $conversionRateList)
        );
    }

    private function getCollectedEventsChart(
        EventList $eventList,
        SummaryEventList $summaryEventList
    ): Chart {
        return $this->chartFactory->create(
            $this->chartDataStructProvider->provideForCollectedEventsChart($eventList, $summaryEventList)
        );
    }

    private function getRevenueChart(
        RevenueList $revenueList,
        Revenue $revenueSummary
    ): Chart {
        return $this->chartFactory->create(
            $this->chartDataStructProvider->provideForRevenueChart($revenueList, $revenueSummary)
        );
    }
}

class_alias(ChartService::class, 'Ibexa\Platform\Personalization\Service\Chart\ChartService');
