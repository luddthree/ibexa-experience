<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Cache;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\LocationPathConverter;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\Personalization\Service\Performance\RecommendationPerformanceServiceInterface;
use Ibexa\Personalization\Value\DateTimeRange;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Performance\Popularity\PopularityList;
use Ibexa\Personalization\Value\Performance\RecommendationEventsDetails;
use Ibexa\Personalization\Value\Performance\RecommendationEventsSummary;
use Ibexa\Personalization\Value\Performance\RecommendationSummary;
use Ibexa\Personalization\Value\Performance\Revenue\RevenueDetailsList;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class CachedRecommendationPerformanceService extends AbstractCacheServiceDecorator implements RecommendationPerformanceServiceInterface
{
    private const RECOMMENDATION_SUMMARY_CACHE_KEY = 'recommendation-summary';
    private const RECOMMENDATION_EVENTS_SUMMARY_CACHE_KEY = 'recommendation-events-summary';
    private const RECOMMENDATION_EVENTS_DETAILS_CACHE_KEY = 'recommendation-events-details';
    private const REVENUE_DETAILS_LIST_CACHE_KEY = 'revenue-details-list';
    private const POPULARITY_LIST_CACHE_KEY = 'popularity-list';

    private RecommendationPerformanceServiceInterface $innerRecommendationPerformanceService;

    public function __construct(
        TagAwareAdapterInterface $cache,
        PersistenceHandler $persistenceHandler,
        PersistenceLogger $persistenceLogger,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        RecommendationPerformanceServiceInterface $innerRecommendationPerformanceService
    ) {
        parent::__construct(
            $cache,
            $persistenceHandler,
            $persistenceLogger,
            $cacheIdentifierGenerator,
            $cacheIdentifierSanitizer,
            $locationPathConverter
        );

        $this->innerRecommendationPerformanceService = $innerRecommendationPerformanceService;
    }

    public function getRecommendationSummary(
        int $customerId,
        ?string $duration = null
    ): RecommendationSummary {
        $arguments = [
            self::RECOMMENDATION_SUMMARY_CACHE_KEY,
            $customerId,
        ];

        if (!empty($duration)) {
            $arguments[] = strtolower($duration);
        }

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::RECOMMENDATION_SUMMARY_CACHE_KEY, $arguments),
            function () use ($customerId, $duration): RecommendationSummary {
                return $this->innerRecommendationPerformanceService->getRecommendationSummary(
                    $customerId,
                    $duration
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getRecommendationEventsSummary(
        int $customerId,
        DateTimeRange $dateTimeRange
    ): RecommendationEventsSummary {
        $arguments = [
            self::RECOMMENDATION_EVENTS_SUMMARY_CACHE_KEY,
            $customerId,
            $dateTimeRange->getFromDate()->getTimestamp(),
            $dateTimeRange->getToDate()->getTimestamp(),
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::RECOMMENDATION_SUMMARY_CACHE_KEY, $arguments),
            function () use ($customerId, $dateTimeRange): RecommendationEventsSummary {
                return $this->innerRecommendationPerformanceService->getRecommendationEventsSummary(
                    $customerId,
                    $dateTimeRange
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getRecommendationEventsDetails(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): RecommendationEventsDetails {
        $arguments = [
            self::RECOMMENDATION_EVENTS_DETAILS_CACHE_KEY,
            $customerId,
            $granularityDateTimeRange->getGranularity(),
            $granularityDateTimeRange->getFromDate()->getTimestamp(),
            $granularityDateTimeRange->getToDate()->getTimestamp(),
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::RECOMMENDATION_EVENTS_DETAILS_CACHE_KEY, $arguments),
            function () use ($customerId, $granularityDateTimeRange): RecommendationEventsDetails {
                return $this->innerRecommendationPerformanceService->getRecommendationEventsDetails(
                    $customerId,
                    $granularityDateTimeRange
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getRevenueDetailsList(
        int $customerId,
        DateTimeRange $dateTimeRange
    ): RevenueDetailsList {
        $arguments = [
            self::REVENUE_DETAILS_LIST_CACHE_KEY,
            $customerId,
            $dateTimeRange->getFromDate()->getTimestamp(),
            $dateTimeRange->getToDate()->getTimestamp(),
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::REVENUE_DETAILS_LIST_CACHE_KEY, $arguments),
            function () use ($customerId, $dateTimeRange): RevenueDetailsList {
                return $this->innerRecommendationPerformanceService->getRevenueDetailsList(
                    $customerId,
                    $dateTimeRange
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getPopularityList(int $customerId, string $duration): PopularityList
    {
        $arguments = [
            self::POPULARITY_LIST_CACHE_KEY,
            $customerId,
            $duration,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::POPULARITY_LIST_CACHE_KEY, $arguments),
            function () use ($customerId, $duration): PopularityList {
                return $this->innerRecommendationPerformanceService->getPopularityList(
                    $customerId,
                    $duration
                );
            },
            self::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }
}

class_alias(CachedRecommendationPerformanceService::class, 'Ibexa\Platform\Personalization\Cache\CachedRecommendationPerformanceService');
