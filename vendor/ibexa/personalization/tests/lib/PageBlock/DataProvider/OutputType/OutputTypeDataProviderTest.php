<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\PageBlock\DataProvider\OutputType;

use Ibexa\Personalization\PageBlock\DataProvider\OutputType\OutputTypeDataProvider;
use Ibexa\Personalization\PageBlock\DataProvider\OutputType\OutputTypeDataProviderInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Tests\Personalization\Scenario\ScenarioListLoader;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\PageBlock\DataProvider\OutputType\OutputTypeDataProvider
 */
final class OutputTypeDataProviderTest extends TestCase
{
    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ScenarioServiceInterface $scenarioService;

    private OutputTypeDataProviderInterface $outputTypeDataProvider;

    protected function setUp(): void
    {
        $this->scenarioService = $this->createMock(ScenarioServiceInterface::class);
        $this->outputTypeDataProvider = new OutputTypeDataProvider($this->scenarioService);
    }

    /**
     * @dataProvider provideDataForTestGetOutputTypeData
     */
    public function testGetOutputTypes(
        ItemTypeList $expectedOutputTypes,
        ScenarioList $scenarioList
    ): void {
        $customerId = 12345;
        $this->mockScenarioServiceGetScenarioList($customerId, $scenarioList);

        self::assertEquals(
            $expectedOutputTypes,
            $this->outputTypeDataProvider->getOutputTypes(12345)
        );
    }

    /**
     * @return iterable<array{
     *  \Ibexa\Personalization\Value\Content\ItemTypeList,
     *  \Ibexa\Personalization\Value\Scenario\ScenarioList
     * }>
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \JsonException
     */
    public function provideDataForTestGetOutputTypeData(): iterable
    {
        yield [
            new ItemTypeList(
                [
                    'All' => new CrossContentType('All'),
                    'Undefined' => new ItemType(1, 'Undefined', []),
                    'Article' => new ItemType(20, 'Article', [20]),
                    'Product' => new ItemType(42, 'Product', [42]),
                ]
            ),
            ScenarioListLoader::getScenarioList(),
        ];

        yield [
            new ItemTypeList([]),
            new ScenarioList([]),
        ];
    }

    private function mockScenarioServiceGetScenarioList(
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
