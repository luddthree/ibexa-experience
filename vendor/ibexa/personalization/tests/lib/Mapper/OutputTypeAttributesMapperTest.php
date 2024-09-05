<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Mapper;

use Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface;
use Ibexa\Personalization\Mapper\OutputTypeAttributesMapper;
use Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\Scenario;
use PHPUnit\Framework\TestCase;

final class OutputTypeAttributesMapperTest extends TestCase
{
    private const CONFIGURED_OUTPUT_TYPES = [
        1 => [
            'title' => 'name',
            'image' => 'image',
        ],
        2 => [
            'title' => 'name',
            'image' => 'image',
        ],
        3 => [
            'title' => 'title',
            'image' => 'img',
        ],
        4 => [
            'title' => 'intro',
            'image' => 'awesome_img',
        ],
    ];

    private OutputTypeAttributesMapperInterface $outputTypeAttributesMapper;

    /** @var \Ibexa\Personalization\Config\OutputType\OutputTypeAttributesResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private OutputTypeAttributesResolverInterface $outputTypeAttributesResolver;

    protected function setUp(): void
    {
        $this->outputTypeAttributesResolver = $this->createMock(OutputTypeAttributesResolverInterface::class);
        $this->outputTypeAttributesMapper = new OutputTypeAttributesMapper($this->outputTypeAttributesResolver);
    }

    /**
     * @dataProvider provideDataForTestGetAttributesByScenario
     *
     * @param array<string> $expectedAttributes
     */
    public function testGetAttributesByScenario(
        array $expectedAttributes,
        Scenario $scenario
    ): void {
        $customerId = 12345;

        $this->mockOutputTypeAttributesResolverResolve($customerId);

        self::assertEquals(
            $expectedAttributes,
            $this->outputTypeAttributesMapper->getAttributesByScenario(12345, $scenario)
        );
    }

    /**
     * @return iterable<array{
     *     array<string>,
     *     \Ibexa\Personalization\Value\Scenario\Scenario
     * }>
     */
    public function provideDataForTestGetAttributesByScenario(): iterable
    {
        yield [
            [],
            $this->createScenario(
                new ItemTypeList(
                    [new ItemType(5, 'foo', [5])]
                )
            ),
        ];

        yield [
            ['name', 'image'],
            $this->createScenario(
                new ItemTypeList(
                    [new ItemType(1, 'foo', [1])]
                )
            ),
        ];

        yield [
            ['name', 'image'],
            $this->createScenario(
                new ItemTypeList(
                    [
                        new ItemType(1, 'foo', [1]),
                        new ItemType(2, 'bar', [2]),
                    ]
                )
            ),
        ];

        yield [
            ['name', 'title', 'image', 'img'],
            $this->createScenario(
                new ItemTypeList(
                    [
                        new ItemType(1, 'foo', [1]),
                        new ItemType(2, 'bar', [2]),
                        new ItemType(3, 'baz', [1, 2, 3]),
                    ]
                )
            ),
        ];

        yield [
            ['name', 'title', 'intro', 'image', 'img', 'awesome_img'],
            $this->createScenario(
                new ItemTypeList(
                    [
                        new ItemType(1, 'foo', [1]),
                        new ItemType(2, 'bar', [2, 3]),
                        new ItemType(3, 'baz', [3]),
                        new ItemType(4, 'test', [1, 4]),
                    ]
                )
            ),
        ];
    }

    private function createScenario(ItemTypeList $itemTypeList): Scenario
    {
        return Scenario::fromArray(
            [
                'referenceCode' => 'foo',
                'type' => 'standard',
                'title' => 'Foo',
                'description' => '',
                'available' => 'AVAILABLE',
                'enabled' => 'ENABLED',
                'inputItemType' => new ItemType(1, 'test', [1]),
                'outputItemTypes' => $itemTypeList,
                'statisticItems' => [],
                'stages' => [],
                'models' => [],
                'websiteContext' => 'auto',
                'profileContext' => 'foo',
            ]
        );
    }

    private function mockOutputTypeAttributesResolverResolve(int $customerId): void
    {
        $this->outputTypeAttributesResolver
            ->expects(self::atLeastOnce())
            ->method('resolve')
            ->with($customerId)
            ->willReturn(self::CONFIGURED_OUTPUT_TYPES);
    }
}
