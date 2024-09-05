<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Provider\Chart;

use DateTimeInterface;
use Ibexa\Personalization\Formatter\NumberFormatterInterface;
use Ibexa\Personalization\Value\Chart\ColorPalette;
use Ibexa\Personalization\Value\Chart\Data;
use Ibexa\Personalization\Value\Chart\DataList;
use Ibexa\Personalization\Value\Chart\LegendItem;
use Ibexa\Personalization\Value\Chart\Struct;
use Ibexa\Personalization\Value\Chart\Summary;
use Ibexa\Personalization\Value\Performance\Details\Event as EventDetails;
use Ibexa\Personalization\Value\Performance\Details\EventList;
use Ibexa\Personalization\Value\Performance\Details\RevenueList;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRate;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRateList;
use Ibexa\Personalization\Value\Performance\Summary\Event;
use Ibexa\Personalization\Value\Performance\Summary\EventList as SummaryEventList;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCall;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCallList;
use Ibexa\Personalization\Value\Performance\Summary\Revenue;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Symfony\Component\Intl\Currencies;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ChartDataStructProvider implements ChartDataStructProviderInterface
{
    private const INTERVAL_1_HOUR = 'PT1H';

    private NumberFormatterInterface $numberFormatter;

    private TranslatorInterface $translator;

    public function __construct(
        NumberFormatterInterface $numberFormatter,
        TranslatorInterface $translator
    ) {
        $this->numberFormatter = $numberFormatter;
        $this->translator = $translator;
    }

    public function provideForRecommendationCallsChart(
        ScenarioList $scenarioList,
        RecommendationCallList $recommendationCallList
    ): Struct {
        $data = [];
        $labels = [];
        $dataList = new DataList();

        /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
        foreach ($scenarioList as $scenario) {
            $recommendationCall = $recommendationCallList->findById($scenario->getReferenceCode());
            if (null !== $recommendationCall) {
                $legendSummary = $this->numberFormatter->shortenNumber(
                    $recommendationCall->getCalls()
                );
            }

            $events = $scenario->getEvents();
            if (null !== $events) {
                foreach ($events as $event) {
                    $labels[] = $this->getDateTimeLabel($event->getTimespanBegin(), $event->getTimespanDuration());
                    $data[$scenario->getReferenceCode()][] = $event->getScenarioCalls();
                }
            }

            $dataList->push(
                $this->getData(
                    $data[$scenario->getReferenceCode()],
                    $scenario->getTitle(),
                    $legendSummary ?? null
                )
            );
        }

        $summary = $recommendationCallList->findById(RecommendationCall::TOTAL);

        return new Struct(
            $labels,
            $dataList,
            [
                new Summary(
                    $this->getTotalLabelForSummary(),
                    $summary ? $this->numberFormatter->shortenNumber($summary->getCalls()) : ''
                ),
            ]
        );
    }

    public function provideForConversionRateChart(
        ScenarioList $scenarioList,
        ConversionRateList $conversionRateList
    ): Struct {
        $data = [];
        $labels = [];
        $dataList = new DataList();

        /** @var \Ibexa\Personalization\Value\Scenario\Scenario $scenario */
        foreach ($scenarioList as $scenario) {
            $conversionRate = $conversionRateList->findById($scenario->getReferenceCode());
            if (null !== $conversionRate) {
                $legendSummary = $this->numberFormatter->shortenNumber(
                    $conversionRate->getPercentage()
                );

                $legendSummary .= '%';
            }

            $events = $scenario->getEvents();
            if (null !== $events) {
                foreach ($events as $event) {
                    $labels[] = $this->getDateTimeLabel($event->getTimespanBegin(), $event->getTimespanDuration());
                    $conversionRatePercent = $event->getConversionRatePercent();
                    if (null !== $conversionRatePercent) {
                        $data[$scenario->getReferenceCode()][] = $conversionRatePercent;
                    }
                }
            }

            $dataList->push(
                $this->getData(
                    $data[$scenario->getReferenceCode()],
                    $scenario->getTitle(),
                    $legendSummary ?? null
                )
            );
        }

        $summary = $conversionRateList->findById(ConversionRate::TOTAL);

        return new Struct(
            $labels,
            $dataList,
            [
                new Summary(
                    $this->getTotalLabelForSummary(),
                    $summary ? $this->numberFormatter->shortenNumber($summary->getPercentage()) . '%' : ''
                ),
            ]
        );
    }

    public function provideForCollectedEventsChart(
        EventList $eventList,
        SummaryEventList $summaryEventList
    ): Struct {
        $dataList = new DataList();
        $labels = [];
        $data = [];

        /** @var \Ibexa\Personalization\Value\Performance\Details\Event $event */
        foreach ($eventList as $event) {
            $labels[] = $this->getDateTimeLabel($event->getTimespanBegin(), $event->getTimespanDuration());
            $data[EventDetails::CLICK][] = $event->getClick();
            $data[EventDetails::PURCHASE][] = $event->getPurchase();
            $data[EventDetails::CLICKED_RECOMMENDED][] = $event->getClickedRecommended();
            $data[EventDetails::CONSUME][] = $event->getConsume();
            $data[EventDetails::RATE][] = $event->getRate();
            $data[EventDetails::RENDERED][] = $event->getRendered();
            $data[EventDetails::BLACKLIST][] = $event->getBlacklist();
            $data[EventDetails::BASKET][] = $event->getBasket();
            $data[EventDetails::PURCHASED_RECOMMENDED][] = $event->getPurchasedRecommended();
        }

        foreach ($data as $eventId => $collection) {
            $summaryEvent = $summaryEventList->findById($eventId);
            if (null !== $summaryEvent) {
                $legendSummary = $this->numberFormatter->shortenNumber($summaryEvent->getHits());
            }

            $dataList->push(
                $this->getData($collection, Event::EVENT_LABELS[$eventId], $legendSummary ?? null)
            );
        }

        $summary = $summaryEventList->findById(Event::TOTAL);

        return new Struct(
            $labels,
            $dataList,
            [
                new Summary(
                    $this->getTotalLabelForSummary(),
                    $summary ? $this->numberFormatter->shortenNumber($summary->getHits()) : ''
                ),
            ],
            ColorPalette::DEFAULT_CHART_COLOR_PALETTE,
            false
        );
    }

    public function provideForRevenueChart(
        RevenueList $revenueList,
        Revenue $revenueSummary
    ): Struct {
        $labels = [];
        $data = [
            'revenue' => [],
            'items_purchased' => [],
        ];
        $summary = [];
        $dataList = new DataList();
        $colors = [];

        /** @var \Ibexa\Personalization\Value\Performance\Details\Revenue $revenue */
        foreach ($revenueList as $revenue) {
            $labels[] = $this->getDateTimeLabel($revenue->getTimespanBegin(), $revenue->getTimespanDuration());
            $data['revenue'][] = $revenue->getRevenue();
            $data['items_purchased'][] = $revenue->getPurchasedRecommended();
        }
        if (array_sum($data['revenue']) > 0) {
            $revenueLabel = $this->translator->trans(
                /** @Desc("Revenue") */
                'chart.revenue',
                [],
                'ibexa_personalization'
            );
            $dataList->push(
                $this->getData($data['revenue'], $revenueLabel)
            );
            $colors[] = ColorPalette::REVENUE_CHART_COLOR;

            $revenueValue = $this->numberFormatter->shortenNumber((float)$revenueSummary->getRevenue());
            $currency = $revenueSummary->getCurrency();

            if (null !== $currency) {
                $revenueValue = Currencies::getSymbol($currency) . $revenueValue;
            }

            $summary[] = new Summary($revenueLabel, $revenueValue);
        }
        if (array_sum($data['items_purchased']) > 0) {
            $itemsPurchasedLabel = $this->translator->trans(
                /** @Desc("Items purchased") */
                'chart.items_purchased',
                [],
                'ibexa_personalization'
            );
            $colors[] = ColorPalette::ITEMS_PURCHASED_CHART_COLOR;
            $dataList->push(
                $this->getData($data['items_purchased'], $itemsPurchasedLabel)
            );
            $summary[] = new Summary($itemsPurchasedLabel, $this->numberFormatter->shortenNumber((float)$revenueSummary->getItemsPurchased()));
        }

        return new Struct(
            $labels,
            $dataList,
            $summary,
            $colors,
            false
        );
    }

    /**
     * @param array<int|float> $data
     */
    private function getData(array $data, string $title, ?string $summary = null): Data
    {
        return new Data(
            new LegendItem(
                $title,
                $summary,
            ),
            $data
        );
    }

    private function getTotalLabelForSummary(): string
    {
        return $this->translator->trans(
            /** @Desc("Total") */
            'chart.total',
            [],
            'ibexa_personalization'
        );
    }

    private function getDateTimeLabel(DateTimeInterface $dateTime, string $granularity): string
    {
        if ($granularity === self::INTERVAL_1_HOUR) {
            return $dateTime->format('H:i');
        }

        return $dateTime->format('d-m');
    }
}

class_alias(ChartDataStructProvider::class, 'Ibexa\Platform\Personalization\Provider\Chart\ChartDataStructProvider');
