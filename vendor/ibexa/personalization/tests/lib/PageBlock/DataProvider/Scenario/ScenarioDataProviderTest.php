<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\PageBlock\DataProvider\Scenario;

use Ibexa\Personalization\PageBlock\DataProvider\Scenario\ScenarioDataProvider;
use Ibexa\Personalization\PageBlock\DataProvider\Scenario\ScenarioDataProviderInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\PageBlock\DataProvider\Scenario\ScenarioDataProvider
 */
final class ScenarioDataProviderTest extends TestCase
{
    private ScenarioDataProviderInterface $scenarioDataProvider;

    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ScenarioServiceInterface $scenarioService;

    protected function setUp(): void
    {
        $this->scenarioService = $this->createMock(ScenarioServiceInterface::class);
        $this->scenarioDataProvider = new ScenarioDataProvider($this->scenarioService);
    }

    /**
     * @dataProvider provideDataForTestGetScenarios
     *
     * @param array<string> $expectedScenarios
     * @phpstan-param \Ibexa\Personalization\Value\Scenario\ScenarioList<
     *  \Ibexa\Personalization\Value\Scenario\Scenario
     * > $scenarioList
     */
    public function testGetScenarios(
        array $expectedScenarios,
        ScenarioList $scenarioList
    ): void {
        $customerId = 12345;

        $this->configureScenarioServiceToReturnScenarioList($customerId, $scenarioList);

        self::assertEquals(
            $expectedScenarios,
            $this->scenarioDataProvider->getScenarios($customerId)
        );
    }

    /**
     * @phpstan-return iterable<array{
     *  array<string>,
     *  \Ibexa\Personalization\Value\Scenario\ScenarioList<
     *      \Ibexa\Personalization\Value\Scenario\Scenario
     *  >
     * }>
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    public function provideDataForTestGetScenarios(): iterable
    {
        yield [
            ['Foo' => 'foo'],
            new ScenarioList(
                [
                    Scenario::fromArray(
                        [
                            'referenceCode' => 'foo',
                            'type' => 'standard',
                            'title' => 'Foo',
                            'description' => '',
                            'available' => 'AVAILABLE',
                            'enabled' => 'ENABLED',
                            'inputItemType' => new ItemType(1, 'test', [1]),
                            'outputItemTypes' => new ItemTypeList([new ItemType(1, 'test', [1])]),
                            'statisticItems' => [],
                            'stages' => [],
                            'models' => [],
                            'websiteContext' => 'auto',
                            'profileContext' => 'foo',
                        ]
                    ),
                ]
            ),
        ];

        yield [
            [
                'Foo' => 'foo',
                'Bar' => 'bar',
                'Baz' => 'baz',
            ],
            $this->getScenarioList(),
        ];

        yield [
            [],
            new ScenarioList([]),
        ];
    }

    /**
     * @return \Ibexa\Personalization\Value\Scenario\ScenarioList<
     *  \Ibexa\Personalization\Value\Scenario\Scenario
     * >
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getScenarioList(): ScenarioList
    {
        return new ScenarioList(
            [
                Scenario::fromArray(
                    [
                        'referenceCode' => 'foo',
                        'type' => 'standard',
                        'title' => 'Foo',
                        'description' => '',
                        'available' => 'AVAILABLE',
                        'enabled' => 'ENABLED',
                        'inputItemType' => new ItemType(1, 'test', [1]),
                        'outputItemTypes' => new ItemTypeList([new ItemType(1, 'test', [1])]),
                        'statisticItems' => [],
                        'stages' => [],
                        'models' => [],
                        'websiteContext' => 'auto',
                        'profileContext' => 'foo',
                    ]
                ),
                Scenario::fromArray(
                    [
                        'referenceCode' => 'bar',
                        'type' => 'standard',
                        'title' => 'Bar',
                        'description' => '',
                        'available' => 'AVAILABLE',
                        'enabled' => 'ENABLED',
                        'inputItemType' => new ItemType(2, 'test', [1]),
                        'outputItemTypes' => new ItemTypeList([new ItemType(2, 'test', [2])]),
                        'statisticItems' => [],
                        'stages' => [],
                        'models' => [],
                        'websiteContext' => 'auto',
                        'profileContext' => 'foo',
                    ]
                ),
                Scenario::fromArray(
                    [
                        'referenceCode' => 'baz',
                        'type' => 'standard',
                        'title' => 'Baz',
                        'description' => '',
                        'available' => 'AVAILABLE',
                        'enabled' => 'ENABLED',
                        'inputItemType' => new ItemType(3, 'test', [3]),
                        'outputItemTypes' => new ItemTypeList([new ItemType(3, 'test', [3])]),
                        'statisticItems' => [],
                        'stages' => [],
                        'models' => [],
                        'websiteContext' => 'auto',
                        'profileContext' => 'foo',
                    ]
                ),
            ]
        );
    }

    /**
     * @phpstan-param \Ibexa\Personalization\Value\Scenario\ScenarioList<
     *  \Ibexa\Personalization\Value\Scenario\Scenario
     * > $scenarioList
     */
    private function configureScenarioServiceToReturnScenarioList(
        int $customerId,
        ScenarioList $scenarioList
    ): void {
        $this->scenarioService
            ->expects(self::once())
            ->method('getScenarioList')
            ->with($customerId)
            ->willReturn($scenarioList);
    }
}
