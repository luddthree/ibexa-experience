<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Cache;

use DateTimeImmutable;
use Ibexa\Personalization\Cache\CachedScenarioService;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Tests\Personalization\Scenario\ScenarioListLoader;

final class CachedScenarioServiceTest extends AbstractCacheTestCase
{
    /** @var \Ibexa\Personalization\Cache\CachedScenarioService */
    private $cachedScenarioService;

    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $innerScenarioService;

    /** @var int */
    private $customerId;

    /** @var \Ibexa\Personalization\Value\GranularityDateTimeRange */
    private $granularityDateTimeRange;

    public function setUp(): void
    {
        parent::setUp();

        $this->innerScenarioService = $this->createMock(ScenarioServiceInterface::class);
        $this->cachedScenarioService = new CachedScenarioService(
            $this->cache,
            $this->persistenceHandler,
            $this->persistenceLogger,
            $this->cacheIdentifierGenerator,
            $this->cacheIdentifierSanitizer,
            $this->locationPathConverter,
            $this->innerScenarioService
        );
        $this->customerId = 1234;
        $this->granularityDateTimeRange = new GranularityDateTimeRange(
            'PT1H',
            new DateTimeImmutable('2020-10-10 12:00:00'),
            new DateTimeImmutable('2020-10-12 12:00:00')
        );
    }

    public function testCreateInstanceCachedScenarioService(): void
    {
        self::assertInstanceOf(
            ScenarioServiceInterface::class,
            $this->cachedScenarioService
        );
    }

    /**
     * @dataProvider providerForTestGetScenarioList
     */
    public function testGetScenarioList(ScenarioList $scenarioList): void
    {
        $cacheKey = 'ibexa-recommendation-scenarios-1234';
        $this->cache
            ->expects(self::once())
            ->method('getItem')
            ->with($cacheKey)
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerScenarioService
            ->method('getScenarioList')
            ->with(
                $this->customerId
            )
            ->willReturn($scenarioList);

        $fromCache = $this->cachedScenarioService->getScenarioList(
            $this->customerId
        );

        self::assertInstanceOf(
            ScenarioList::class,
            $fromCache
        );
        self::assertEquals(
            $scenarioList,
            $fromCache
        );
    }

    public function providerForTestGetScenarioList(): iterable
    {
        yield [ScenarioListLoader::getScenarioList()];
    }

    /**
     * @dataProvider providerForTestGetCalledScenarios
     */
    public function testGetCalledScenarios(ScenarioList $scenarioList): void
    {
        $cacheKey = 'ibexa-recommendation-called-scenarios-1234-PT1H-1602331200-1602504000';
        $this->cache
            ->expects(self::once())
            ->method('getItem')
            ->with(
                $cacheKey
            )
            ->willReturn(
                $this->getCacheItem($cacheKey)
            );

        $this->innerScenarioService
            ->method('getCalledScenarios')
            ->with(
                $this->customerId,
                new GranularityDateTimeRange(
                    'PT1H',
                    new DateTimeImmutable('2020-10-10 12:00:00'),
                    new DateTimeImmutable('2020-10-12 12:00:00'),
                )
            )
            ->willReturn($scenarioList);

        $fromCache = $this->cachedScenarioService->getCalledScenarios(
            $this->customerId,
            $this->granularityDateTimeRange
        );

        self::assertInstanceOf(
            ScenarioList::class,
            $fromCache
        );
        self::assertEquals(
            $scenarioList,
            $fromCache
        );
    }

    public function providerForTestGetCalledScenarios(): iterable
    {
        $scenarioList = [];

        foreach (ScenarioListLoader::getScenarioList() as $scenario) {
            if ($scenario->getCalls() > 0) {
                $scenarioList[] = $scenario;
            }
        }

        yield [
            new ScenarioList($scenarioList),
        ];
    }

    public function testGetScenarioListByScenarioType(): void
    {
        $cacheKey = 'ibexa-recommendation-scenarios-type-1234-commerce';
        $scenarioList = ScenarioListLoader::getCommerceScenarioList();
        $scenarioType = 'commerce';

        $this->mockCacheGetItem($cacheKey);
        $this->mockInnerScenarioServiceGetScenarioListByScenarioType($scenarioType, $scenarioList);

        self::assertEquals(
            $scenarioList,
            $this->cachedScenarioService->getScenarioListByScenarioType($this->customerId, $scenarioType)
        );
    }

    private function mockCacheGetItem(string $cacheKey): void
    {
        $this->cache
            ->expects(self::once())
            ->method('getItem')
            ->with($cacheKey)
            ->willReturn($this->getCacheItem($cacheKey));
    }

    private function mockInnerScenarioServiceGetScenarioListByScenarioType(
        string $scenarioType,
        ScenarioList $scenarioList
    ): void {
        $this->innerScenarioService
            ->method('getScenarioListByScenarioType')
            ->with(
                $this->customerId,
                $scenarioType
            )
            ->willReturn($scenarioList);
    }
}

class_alias(CachedScenarioServiceTest::class, 'Ibexa\Platform\Tests\Personalization\Cache\CachedScenarioServiceTest');
