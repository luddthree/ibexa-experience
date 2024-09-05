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
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcher;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class CachedScenarioService extends AbstractCacheServiceDecorator implements ScenarioServiceInterface
{
    private const SCENARIO_KEY = 'recommendation-scenario';
    private const SCENARIOS_KEY = 'recommendation-scenarios';
    private const SCENARIOS_BY_TYPE_KEY = 'recommendation-scenarios-type';
    private const CALLED_SCENARIOS_KEY = 'recommendation-called-scenarios';

    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface */
    private $innerScenarioService;

    public function __construct(
        TagAwareAdapterInterface $cache,
        PersistenceHandler $persistenceHandler,
        PersistenceLogger $logger,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        ScenarioServiceInterface $innerScenarioService
    ) {
        parent::__construct(
            $cache,
            $persistenceHandler,
            $logger,
            $cacheIdentifierGenerator,
            $cacheIdentifierSanitizer,
            $locationPathConverter
        );

        $this->innerScenarioService = $innerScenarioService;
    }

    public function getScenarioList(
        int $customerId,
        ?GranularityDateTimeRange $granularityDateTimeRange = null
    ): ScenarioList {
        $arguments = [
            self::SCENARIOS_KEY,
            $customerId,
        ];

        if (null !== $granularityDateTimeRange) {
            $arguments[] = $granularityDateTimeRange->getGranularity();
            $arguments[] = $granularityDateTimeRange->getFromDate()->getTimestamp();
            $arguments[] = $granularityDateTimeRange->getToDate()->getTimestamp();
        }

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SCENARIOS_KEY, $arguments),
            function () use ($customerId, $granularityDateTimeRange): ScenarioList {
                return $this->innerScenarioService->getScenarioList(
                    $customerId,
                    $granularityDateTimeRange
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getCalledScenarios(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): ScenarioList {
        $arguments = [
            self::CALLED_SCENARIOS_KEY,
            $customerId,
            $granularityDateTimeRange->getGranularity(),
            $granularityDateTimeRange->getFromDate()->getTimestamp(),
            $granularityDateTimeRange->getToDate()->getTimestamp(),
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::CALLED_SCENARIOS_KEY, $arguments),
            function () use ($customerId, $granularityDateTimeRange): ScenarioList {
                return $this->innerScenarioService->getCalledScenarios(
                    $customerId,
                    $granularityDateTimeRange
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getScenarioListByScenarioType(int $customerId, string $scenarioType): ScenarioList
    {
        $arguments = [
            self::SCENARIOS_BY_TYPE_KEY,
            $customerId,
            $scenarioType,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SCENARIOS_BY_TYPE_KEY, $arguments),
            function () use ($customerId, $scenarioType): ScenarioList {
                return $this->innerScenarioService->getScenarioListByScenarioType(
                    $customerId,
                    $scenarioType
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getScenario(
        int $customerId,
        string $scenarioName
    ): Scenario {
        $arguments = [
            self::SCENARIO_KEY,
            $customerId,
            $scenarioName,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SCENARIO_KEY, $arguments),
            function () use ($customerId, $scenarioName): Scenario {
                return $this->innerScenarioService->getScenario(
                    $customerId,
                    $scenarioName
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function createScenario(
        int $customerId,
        Scenario $scenario
    ): Scenario {
        $arguments = [
            self::SCENARIO_KEY,
            $customerId,
            $scenario->getReferenceCode(),
        ];
        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SCENARIO_KEY, $arguments),
            function () use ($customerId, $scenario): Scenario {
                return $this->innerScenarioService->createScenario($customerId, $scenario);
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        $this->removeScenarioListCache($customerId);

        return $item->get();
    }

    public function updateScenario(
        int $customerId,
        Scenario $scenario
    ): Scenario {
        $scenario = $this->innerScenarioService->updateScenario($customerId, $scenario);

        $this->removeScenarioListCache($customerId);
        $this->removeCacheItem(
            self::SCENARIO_KEY,
            [
                self::SCENARIO_KEY,
                $customerId,
                $scenario->getReferenceCode(),
            ]
        );

        return $scenario;
    }

    public function deleteScenario(int $customerId, string $scenarioName): int
    {
        $scenario = $this->innerScenarioService->deleteScenario($customerId, $scenarioName);

        $this->removeCacheItem(
            self::SCENARIO_KEY,
            [
                self::SCENARIO_KEY,
                $customerId,
                $scenarioName,
            ]
        );
        $this->removeScenarioListCache($customerId);

        return $scenario;
    }

    public function getScenarioProfileFilterSet(
        int $customerId,
        string $scenarioName
    ): ProfileFilterSet {
        $arguments = [
            self::SCENARIO_KEY,
            $customerId,
            $scenarioName,
            ScenarioDataFetcher::SCENARIO_FILTER_PROFILE,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SCENARIO_KEY, $arguments),
            function () use ($customerId, $scenarioName): ProfileFilterSet {
                return $this->innerScenarioService->getScenarioProfileFilterSet(
                    $customerId,
                    $scenarioName
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getScenarioStandardFilterSet(
        int $customerId,
        string $scenarioName
    ): StandardFilterSet {
        $arguments = [
            self::SCENARIO_KEY,
            $customerId,
            $scenarioName,
            ScenarioDataFetcher::SCENARIO_FILTER_STANDARD,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SCENARIO_KEY, $arguments),
            function () use ($customerId, $scenarioName): StandardFilterSet {
                return $this->innerScenarioService->getScenarioStandardFilterSet(
                    $customerId,
                    $scenarioName
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function updateScenarioProfileFilterSet(
        int $customerId,
        string $scenarioName,
        ProfileFilterSet $profileFilterSet
    ): ProfileFilterSet {
        return $this->innerScenarioService->updateScenarioProfileFilterSet(
            $customerId,
            $scenarioName,
            $profileFilterSet
        );
    }

    public function updateScenarioStandardFilterSet(
        int $customerId,
        string $scenarioName,
        StandardFilterSet $standardFilterSet
    ): StandardFilterSet {
        return $this->innerScenarioService->updateScenarioStandardFilterSet(
            $customerId,
            $scenarioName,
            $standardFilterSet
        );
    }

    private function removeScenarioListCache(int $customerId): void
    {
        $this->removeCacheItem(
            self::SCENARIOS_KEY,
            [
                self::SCENARIOS_KEY,
                $customerId,
            ]
        );
    }
}

class_alias(CachedScenarioService::class, 'Ibexa\Platform\Personalization\Cache\CachedScenarioService');
