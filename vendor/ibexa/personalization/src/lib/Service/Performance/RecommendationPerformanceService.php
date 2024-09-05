<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Performance;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Personalization\Client\Consumer\Performance\EventsDetailsDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\EventsSummaryDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\PopularityDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\RevenueDetailsDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Performance\SummaryDataFetcherInterface;
use Ibexa\Personalization\Config\Repository\RepositoryConfigResolverInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\Details\Event;
use Ibexa\Personalization\Value\Performance\Details\EventList;
use Ibexa\Personalization\Value\Performance\Details\Revenue;
use Ibexa\Personalization\Value\Performance\Details\RevenueList;
use Ibexa\Personalization\Value\Performance\Popularity\Popularity;
use Ibexa\Personalization\Value\Performance\Popularity\PopularityList;
use Ibexa\Personalization\Value\Performance\RecommendationEventsDetails;
use Ibexa\Personalization\Value\Performance\RecommendationEventsSummary;
use Ibexa\Personalization\Value\Performance\RecommendationSummary;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetails;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRate;
use Ibexa\Personalization\Value\Performance\Summary\ConversionRateList;
use Ibexa\Personalization\Value\Performance\Summary\Event as EventSummary;
use Ibexa\Personalization\Value\Performance\Summary\EventList as SummaryEventList;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCall as RecommendationCallSummary;
use Ibexa\Personalization\Value\Performance\Summary\RecommendationCallList as RecommendationCallListSummary;
use Ibexa\Personalization\Value\Performance\Summary\Revenue as RevenueSummary;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class RecommendationPerformanceService implements RecommendationPerformanceServiceInterface
{
    private ContentService $contentService;

    private EventsDetailsDataFetcherInterface $eventsDetailsDataFetcher;

    private EventsSummaryDataFetcherInterface $eventsSummaryDataFetcher;

    private PopularityDataFetcherInterface $popularityDataFetcher;

    private RepositoryConfigResolverInterface $repositoryConfigResolver;

    private RevenueDetailsDataFetcherInterface $revenueDetailsDataFetcher;

    private SettingServiceInterface $settingService;

    private SummaryDataFetcherInterface $summaryDataFetcher;

    public function __construct(
        ContentService $contentService,
        EventsDetailsDataFetcherInterface $eventsDetailsDataFetcher,
        EventsSummaryDataFetcherInterface $eventsSummaryDataFetcher,
        PopularityDataFetcherInterface $popularityDataFetcher,
        RepositoryConfigResolverInterface $repositoryConfigResolver,
        RevenueDetailsDataFetcherInterface $revenueDetailsDataFetcher,
        SettingServiceInterface $settingService,
        SummaryDataFetcherInterface $summaryDataFetcher
    ) {
        $this->contentService = $contentService;
        $this->eventsDetailsDataFetcher = $eventsDetailsDataFetcher;
        $this->eventsSummaryDataFetcher = $eventsSummaryDataFetcher;
        $this->popularityDataFetcher = $popularityDataFetcher;
        $this->repositoryConfigResolver = $repositoryConfigResolver;
        $this->revenueDetailsDataFetcher = $revenueDetailsDataFetcher;
        $this->settingService = $settingService;
        $this->summaryDataFetcher = $summaryDataFetcher;
    }

    public function getRecommendationSummary(
        int $customerId,
        ?string $duration = null
    ): RecommendationSummary {
        $response = $this->summaryDataFetcher->fetchRecommendationSummary(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $duration
        );

        return RecommendationSummary::fromArray(
            json_decode($response->getBody()->getContents(), true)
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getRecommendationEventsSummary(
        int $customerId,
        DateTimeRange $dateTimeRange
    ): RecommendationEventsSummary {
        $response = $this->eventsSummaryDataFetcher->fetchRecommendationEventsSummary(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $dateTimeRange
        );

        $responseContents = json_decode($response->getBody()->getContents(), true);

        return new RecommendationEventsSummary(
            $this->getRecommendationCallListSummary($responseContents['recoCallDetails']),
            $this->getConversionSummary($responseContents['conversionDetails']),
            $this->getEventsSummary($responseContents['eventsDetails']),
            $this->getRevenueSummary($responseContents['revenueDetails'])
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getRecommendationEventsDetails(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): RecommendationEventsDetails {
        $response = $this->eventsDetailsDataFetcher->fetchRecommendationEventsDetails(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $granularityDateTimeRange
        );

        $responseContents = json_decode($response->getBody()->getContents(), true);
        $events = $this->getEvents($responseContents);

        return new RecommendationEventsDetails(
            new RevenueList($events['revenueList']),
            new EventList($events['eventList'])
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function getRevenueDetailsList(
        int $customerId,
        DateTimeRange $dateTimeRange
    ): RevenueDetailsList {
        $response = $this->revenueDetailsDataFetcher->fetchRevenueDetailsList(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $dateTimeRange
        );

        $responseContents = json_decode($response->getBody()->getContents(), true);
        $revenueDetailsList = $this->getRevenueDetails($responseContents);

        return new RevenueDetailsList($revenueDetailsList);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     */
    public function getPopularityList(int $customerId, string $duration): PopularityList
    {
        try {
            $licenseKey = $this->settingService->getLicenceKeyByCustomerId($customerId);
            $response = $this->popularityDataFetcher->fetchPopularityList(
                $customerId,
                $licenseKey,
                $duration
            );

            $responseContents = json_decode(
                $response->getBody()->getContents(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
            $popularityList = [];

            foreach ($responseContents as $popularity) {
                if (!isset($popularity['item']['title'])) {
                    $id = $popularity['item']['id'];
                    $title = $this->repositoryConfigResolver->useRemoteId()
                        ? $this->getItemTypeTitleForContentRemoteId((string) $id)
                        : $this->getItemTypeTitleForContentId((int) $id);

                    $popularity['item']['title'] = $title;
                }

                $popularityList[] = Popularity::fromArray($popularity);
            }

            return new PopularityList($popularityList);
        } catch (BadResponseException $exception) {
            $allowedExceptionCodes = [
                Response::HTTP_NOT_FOUND,
                Response::HTTP_SERVICE_UNAVAILABLE,
            ];
            if (in_array($exception->getCode(), $allowedExceptionCodes, true)) {
                return new PopularityList([]);
            }

            throw $exception;
        }
    }

    /**
     * @phpstan-param array<int, array{
     *      'timeRecommended': int|string,
     *      'timeConsumed': int|string,
     *      'item': array{
     *          'id': int|string,
     *          'type': int,
     *          'title': ?string,
     *      },
     *      'price'?: int|string,
     *      'quantity'?: int|string,
     *      'currency'?: int|string,
     * }> $responseContents
     *
     * @return array<RevenueDetails>
     */
    private function getRevenueDetails(array $responseContents): array
    {
        $revenueDetailsList = [];

        foreach ($responseContents as $revenue) {
            if (!isset($revenue['item']['title'])) {
                $id = $revenue['item']['id'];

                $title = $this->repositoryConfigResolver->useRemoteId()
                    ? $this->getItemTypeTitleForContentRemoteId((string) $id)
                    : $this->getItemTypeTitleForContentId((int) $id);

                $revenue['item']['title'] = $title;
            }

            $revenueDetailsList[] = RevenueDetails::fromArray($revenue);
        }

        return $revenueDetailsList;
    }

    private function getItemTypeTitleForContentId(int $itemTypeId): ?string
    {
        try {
            return $this->contentService->loadContent($itemTypeId)->getName();
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    private function getItemTypeTitleForContentRemoteId(string $itemTypeRemoteId): ?string
    {
        try {
            return $this->contentService->loadContentByRemoteId($itemTypeRemoteId)->getName();
        } catch (NotFoundException $exception) {
            return null;
        }
    }

    /**
     * @phpstan-param array<int, array{
     *      'scenarioNames': array<int, string>,
     *      'percentage': int|string,
     *      'calls': int|string,
     * }> $properties
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getRecommendationCallListSummary(array $properties): RecommendationCallListSummary
    {
        $recommendationCalls = [];

        foreach ($properties as $propertyKey => $propertyValue) {
            $recommendationCalls[] = RecommendationCallSummary::fromArray(
                [
                    'id' => $propertyKey,
                    'name' => $propertyValue['scenarioNames'][0] ?? $propertyKey,
                    'percentage' => $propertyValue['percentage'],
                    'calls' => $propertyValue['calls'],
                ]
            );
        }

        return new RecommendationCallListSummary($recommendationCalls);
    }

    /**
     * @phpstan-param array<int, array{
     *      'scenarioNames': array<int, string>,
     *      'value': int|string,
     * }> $properties
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getConversionSummary(array $properties): ConversionRateList
    {
        $conversionRateList = [];

        foreach ($properties as $conversionRateName => $conversionRateParameters) {
            $conversionRateList[] = ConversionRate::fromArray(
                [
                    'id' => $conversionRateName,
                    'name' => $conversionRateParameters['scenarioNames'][0] ?? $conversionRateName,
                    'percentage' => $conversionRateParameters['value'],
                ]
            );
        }

        return new ConversionRateList($conversionRateList);
    }

    /**
     * @param array<string, int> $properties
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getEventsSummary(array $properties): SummaryEventList
    {
        $events = [];

        foreach ($properties as $eventName => $hits) {
            $events[] = EventSummary::fromArray(
                [
                    'name' => mb_strtolower($eventName),
                    'hits' => (int)$hits,
                ]
            );
        }

        return new SummaryEventList($events);
    }

    /**
     * @phpstan-param array{
     *  'currency'?: string|null,
     *  'items_purchased'?: float|int|string|null,
     *  'revenue'?: float|int|string|null,
     * } $properties
     */
    private function getRevenueSummary(array $properties): RevenueSummary
    {
        return RevenueSummary::fromArray($properties);
    }

    /**
     * @phpstan-param array<array{
     *  'clickEvents': int,
     *  'purchaseEvents': int,
     *  'clickedRecommended': int,
     *  'consumeEvents': int,
     *  'rateEvents': int,
     *  'renderedEvents': int,
     *  'blacklistEvents': int,
     *  'basketEvents': int,
     *  'revenue': int,
     *  'purchasedRecommended': int,
     *  'timespanBegin': int|string,
     *  'timespanDuration': int|string,
     * }> $responseContents
     *
     * @phpstan-return array{
     *  'revenueList': array<\Ibexa\Personalization\Value\Performance\Details\Revenue>,
     *  'eventList': array<\Ibexa\Personalization\Value\Performance\Details\Event>,
     * }
     */
    private function getEvents(array $responseContents): array
    {
        $events = [
            'revenueList' => [],
            'eventList' => [],
        ];

        foreach ($responseContents as $event) {
            $events['revenueList'][] = $this->getRevenue($event);
            $events['eventList'][] = $this->getEvent($event);
        }

        return $events;
    }

    /**
     * @phpstan-param array{
     *  'clickEvents': int,
     *  'purchaseEvents': int,
     *  'clickedRecommended': int,
     *  'consumeEvents': int,
     *  'rateEvents': int,
     *  'renderedEvents': int,
     *  'blacklistEvents': int,
     *  'basketEvents': int,
     *  'revenue': int,
     *  'purchasedRecommended': int,
     *  'timespanBegin': int|string,
     *  'timespanDuration': int|string,
     * } $event
     */
    private function getRevenue(array $event): Revenue
    {
        return Revenue::fromArray([
            'revenue' => $event['revenue'],
            'purchased_recommended' => $event['purchasedRecommended'],
            'timespan_begin' => $event['timespanBegin'],
            'timespan_duration' => $event['timespanDuration'],
        ]);
    }

    /**
     * @phpstan-param array{
     *  'clickEvents': int,
     *  'purchaseEvents': int,
     *  'clickedRecommended': int,
     *  'consumeEvents': int,
     *  'rateEvents': int,
     *  'renderedEvents': int,
     *  'blacklistEvents': int,
     *  'basketEvents': int,
     *  'revenue': int,
     *  'purchasedRecommended': int,
     *  'timespanBegin': int|string,
     *  'timespanDuration': int|string,
     * } $event
     */
    private function getEvent(array $event): Event
    {
        return Event::fromArray([
            Event::CLICK => $event['clickEvents'],
            Event::PURCHASE => $event['purchaseEvents'],
            Event::CLICKED_RECOMMENDED => $event['clickedRecommended'],
            Event::CONSUME => $event['consumeEvents'],
            Event::RATE => $event['rateEvents'],
            Event::RENDERED => $event['renderedEvents'],
            Event::BLACKLIST => $event['blacklistEvents'],
            Event::BASKET => $event['basketEvents'],
            Event::PURCHASED_RECOMMENDED => $event['purchasedRecommended'],
            Event::TIMESPAN_BEGIN => $event['timespanBegin'],
            Event::TIMESPAN_DURATION => $event['timespanDuration'],
        ]);
    }
}

class_alias(RecommendationPerformanceService::class, 'Ibexa\Platform\Personalization\Service\Performance\RecommendationPerformanceService');
