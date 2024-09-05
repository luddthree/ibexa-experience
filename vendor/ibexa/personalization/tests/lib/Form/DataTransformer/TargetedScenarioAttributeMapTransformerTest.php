<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\DataTransformer;

use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Personalization\Form\DataTransformer\TargetedScenarioAttributeMapTransformer;
use Ibexa\Personalization\Security\Service\SecurityServiceInterface;
use Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Segmentation\Value\Segment;
use Ibexa\Segmentation\Value\SegmentGroup;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @covers \Ibexa\Personalization\Form\DataTransformer\TargetedScenarioAttributeMapTransformer
 */
final class TargetedScenarioAttributeMapTransformerTest extends TestCase
{
    /** @var \Ibexa\Personalization\Service\Scenario\ScenarioServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ScenarioServiceInterface $scenarioService;

    /** @var \Ibexa\Personalization\Security\Service\SecurityServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SecurityServiceInterface $securityService;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SegmentationServiceInterface $segmentationService;

    /** @var \Symfony\Component\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SerializerInterface $serializer;

    private TargetedScenarioAttributeMapTransformer $targetedScenarioAttributeMapTransformer;

    protected function setUp(): void
    {
        $this->scenarioService = $this->createMock(ScenarioServiceInterface::class);
        $this->securityService = $this->createMock(SecurityServiceInterface::class);
        $this->segmentationService = $this->createMock(SegmentationServiceInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);

        $this->targetedScenarioAttributeMapTransformer = new TargetedScenarioAttributeMapTransformer(
            $this->scenarioService,
            $this->securityService,
            $this->segmentationService,
            $this->serializer
        );
    }

    /**
     * @dataProvider provideDataForTestTransformThrowTransformationFailedException
     *
     * @throws \JsonException
     */
    public function testTransformThrowTransformationFailedException(string $value): void
    {
        $this->mockSecurityServiceGetCurrentCustomerId(12345);

        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Keys \'segmentId\' or \'scenario\' don\'t exist in input data.');

        $this->targetedScenarioAttributeMapTransformer->transform($value);
    }

    /**
     * @dataProvider provideDataForTestReverseTransformThrowTransformationFailedException
     */
    public function testReverseTransformThrowTransformationFailedException(string $value): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Can\'t find Segment and Scenario data.');

        $this->targetedScenarioAttributeMapTransformer->reverseTransform($value);
    }

    /**
     * @return iterable<array{string}>
     */
    public function provideDataForTestTransformThrowTransformationFailedException(): iterable
    {
        yield [
            '[{"foo":"value","bar":"value"}]',
            '[{"segmentId":"foo","bar":"baz"}]',
            '[{"foo":"bar","scenario":"value"}]',
        ];
    }

    /**
     * @return iterable<array{string}>
     */
    public function provideDataForTestReverseTransformThrowTransformationFailedException(): iterable
    {
        yield [
            '[{"foo":{"id":1},"bar":{"baz":"value"}}]',
            '[{"segment":{"id":1},"foo":{"bar":"baz"}}]',
            '[{"foo":{"id":1},"scenario":{"referenceCode":"bar"}}]',
        ];
    }

    /**
     * @dataProvider provideDataForTestTransform
     *
     * @param array<array{int, \Ibexa\Segmentation\Value\Segment}> $segmentsReturnMap
     * @param array<array{int, string, \Ibexa\Personalization\Value\Scenario\Scenario}> $scenarioReturnMap
     * @param array<array{array<array<string>>, string, array<string>, string}> $serializerReturnMap
     */
    public function testTransform(
        ?string $expected,
        ?string $value,
        ?int $customerId,
        array $segmentsReturnMap,
        array $scenarioReturnMap,
        array $serializerReturnMap
    ): void {
        if (null !== $value) {
            $this->mockSecurityServiceGetCurrentCustomerId($customerId);
        }

        if (null !== $customerId) {
            $this->mockSegmentationServiceLoadSegment($segmentsReturnMap);
            $this->mockScenarioServiceGetScenario($scenarioReturnMap);
            $this->mockSerializerSerialize($serializerReturnMap);
        }

        self::assertEquals(
            $expected,
            $this->targetedScenarioAttributeMapTransformer->transform($value)
        );
    }

    /**
     * @return iterable<array{
     *     ?string,
     *     ?string,
     *     ?int,
     *     array<array{int, \Ibexa\Segmentation\Value\Segment}>,
     *     array<array{int, string, \Ibexa\Personalization\Value\Scenario\Scenario}>,
     * }>
     */
    public function provideDataForTestTransform(): iterable
    {
        yield [
            '[]',
            null,
            null,
            [],
            [],
            [],
        ];

        yield [
            null,
            '[{"segmentId":1,"scenario":"foo","outputType":""}]',
            null,
            [],
            [],
            [],
        ];

        yield [
            '["segment":{"id":1,"name":"Foo"},"scenario":{"referenceCode":"foo","title":"Foo"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}}]',
            '[{"segmentId":1,"scenario":"foo","outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}}]',
            12345,
            [
                [1, $this->createTestSegment(1, 'Foo')],
            ],
            [
                [12345, 'foo', $this->createTestScenario('foo', 'Foo')],
            ],
            [
                [
                    [
                        [
                            'segment' => [
                                'id' => 1,
                                'name' => 'Foo',
                            ],
                            'scenario' => [
                                'referenceCode' => 'foo',
                                'title' => 'Foo',
                            ],
                            'outputType' => [
                                'type' => 'itemType',
                                'id' => 1,
                                'contentTypes' => ['1'],
                                'description' => 'Foo',
                            ],
                        ],
                    ],
                    'json',
                    [],
                    '["segment":{"id":1,"name":"Foo"},"scenario":{"referenceCode":"foo","title":"Foo"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}}]',
                ],
            ],
        ];

        yield [
            '[{"segment":{"id":1,"name":"Foo"},"scenario":{"referenceCode":"foo","title":"Foo"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}},{"segment":{"id":2,"name":"Bar"},"scenario":{"referenceCode":"bar","title":"Bar"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}},{"segment":{"id":3,"name":"Baz"},"scenario":{"referenceCode":"baz","title":"Baz"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}}]',
            '[{"segmentId":1,"scenario":"foo","outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}},{"segmentId":2,"scenario":"bar","outputType":{"type":"itemType","id":2,"contentTypes":["2"],"description":"Bar"}},{"segmentId":3,"scenario":"baz","outputType":{"type":"itemType","id":3,"contentTypes":["3"],"description":"Baz"}}]',
            12345,
            [
                [1, $this->createTestSegment(1, 'Foo')],
                [2, $this->createTestSegment(2, 'Bar')],
                [3, $this->createTestSegment(3, 'Baz')],
            ],
            [
                [12345, 'foo', $this->createTestScenario('foo', 'Foo')],
                [12345, 'bar', $this->createTestScenario('bar', 'Bar')],
                [12345, 'baz', $this->createTestScenario('baz', 'Baz')],
            ],
            [
                [
                    [
                        [
                            'segment' => [
                                'id' => 1,
                                'name' => 'Foo',
                            ],
                            'scenario' => [
                                'referenceCode' => 'foo',
                                'title' => 'Foo',
                            ],
                            'outputType' => [
                                'type' => 'itemType',
                                'id' => 1,
                                'contentTypes' => ['1'],
                                'description' => 'Foo',
                            ],
                        ],
                        [
                            'segment' => [
                                'id' => 2,
                                'name' => 'Bar',
                            ],
                            'scenario' => [
                                'referenceCode' => 'bar',
                                'title' => 'Bar',
                            ],
                            'outputType' => [
                                'type' => 'itemType',
                                'id' => 2,
                                'contentTypes' => ['2'],
                                'description' => 'Bar',
                            ],
                        ],
                        [
                            'segment' => [
                                'id' => 3,
                                'name' => 'Baz',
                            ],
                            'scenario' => [
                                'referenceCode' => 'baz',
                                'title' => 'Baz',
                            ],
                            'outputType' => [
                                'type' => 'itemType',
                                'id' => 3,
                                'contentTypes' => ['3'],
                                'description' => 'Baz',
                            ],
                        ],
                    ],
                    'json',
                    [],
                    '[{"segment":{"id":1,"name":"Foo"},"scenario":{"referenceCode":"foo","title":"Foo"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}},{"segment":{"id":2,"name":"Bar"},"scenario":{"referenceCode":"bar","title":"Bar"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}},{"segment":{"id":3,"name":"Baz"},"scenario":{"referenceCode":"baz","title":"Baz"},{"outputType":{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}}]',
                ],
            ],
        ];
    }

    private function createTestScenario(string $referenceCode, string $title): Scenario
    {
        $itemType = new ItemType(1, 'foo', [1]);

        return Scenario::fromArray(
            [
                'referenceCode' => $referenceCode,
                'type' => 'standard',
                'title' => $title,
                'available' => 'AVAILABLE',
                'enabled' => 'ENABLED',
                'inputItemType' => $itemType,
                'outputItemTypes' => new ItemTypeList([$itemType]),
                'description' => 'Test scenario',
            ]
        );
    }

    private function createTestSegment(int $id, string $name): Segment
    {
        return new Segment(
            [
                'id' => $id,
                'identifier' => 'some_identifier',
                'name' => $name,
                'group' => new SegmentGroup(
                    [
                        'id' => 1,
                        'identifier' => 'foo_group',
                        'name' => 'Foo group',
                    ]
                ),
            ]
        );
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     */
    public function testReverseTransform(
        ?string $expected,
        ?string $value
    ): void {
        self::assertEquals(
            $expected,
            $this->targetedScenarioAttributeMapTransformer->reverseTransform($value)
        );
    }

    /**
     * @return iterable<array{
     *     string,
     *     ?string
     * }>
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield ['[]', null];

        yield ['[]', ''];

        yield [
            '[{"segmentId":1,"scenario":"foo","outputType":{"type":"crossContentType","description":"Bar"}}]',
            '[{"segment":{"id":1},"scenario":{"referenceCode":"foo"},"outputType":{"type":"crossContentType","description":"Bar"}}]',
        ];

        yield [
            '[{"segmentId":1,"scenario":"foo","outputType":{"type":"crossContentType","description":"Bar"}},{"segmentId":2,"scenario":"bar","outputType":{"type":"crossContentType","description":"Bar"}},{"segmentId":3,"scenario":"baz","outputType":{"type":"crossContentType","description":"Bar"}}]',
            '[{"segment":{"id":1},"scenario":{"referenceCode":"foo"},"outputType":{"type":"crossContentType","description":"Bar"}},{"segment":{"id":2},"scenario":{"referenceCode":"bar"},"outputType":{"type":"crossContentType","description":"Bar"}},{"segment":{"id":3},"scenario":{"referenceCode":"baz"},"outputType":{"type":"crossContentType","description":"Bar"}}]',
        ];
    }

    private function mockSecurityServiceGetCurrentCustomerId(?int $customerId): void
    {
        $this->securityService
            ->expects(self::once())
            ->method('getCurrentCustomerId')
            ->willReturn($customerId);
    }

    /**
     * @param array<array{int, \Ibexa\Segmentation\Value\Segment}> $returnMap
     */
    private function mockSegmentationServiceLoadSegment(array $returnMap): void
    {
        $this->segmentationService
            ->expects(self::atLeastOnce())
            ->method('loadSegment')
            ->willReturnMap($returnMap);
    }

    /**
     * @param array<array{int, string, \Ibexa\Personalization\Value\Scenario\Scenario}> $returnMap
     */
    private function mockScenarioServiceGetScenario(array $returnMap): void
    {
        $this->scenarioService
            ->expects(self::atLeastOnce())
            ->method('getScenario')
            ->willReturnMap($returnMap);
    }

    /**
     * @param array<array{array<array<string>>, string, array<string>, string}> $returnMap
     */
    private function mockSerializerSerialize(array $returnMap): void
    {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('serialize')
            ->willReturnMap($returnMap);
    }
}
