<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Form\DataTransformer;

use Ibexa\Personalization\Form\DataTransformer\OutputTypeListAttributeTransformer;
use Ibexa\Personalization\Value\Content\AbstractItemType;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @covers \Ibexa\Personalization\Form\DataTransformer\OutputTypeListAttributeTransformer
 */
final class OutputTypeListAttributeTransformerTest extends TestCase
{
    private OutputTypeListAttributeTransformer $transformer;

    /** @var \Symfony\Component\Serializer\SerializerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->transformer = new OutputTypeListAttributeTransformer($this->serializer);
    }

    public function testTransformThrowTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Invalid data. Value should be type of: string. int given.');

        $this->transformer->transform(12345);
    }

    public function testReverseTransformThrowTransformationFailedException(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid data. Value should be type of: %s. array given.',
            AbstractItemType::class
        ));

        $this->transformer->reverseTransform([]);
    }

    /**
     * @dataProvider provideDataForTestTransform
     */
    public function testTransform(AbstractItemType $expected, string $value): void
    {
        $this->mockSerializerDeserialize($value, $expected);

        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     */
    public function testReverseTransform(string $expected, AbstractItemType $value): void
    {
        $this->mockSerializerSerialize($value, $expected);

        self::assertEquals($expected, $this->transformer->reverseTransform($value));
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     *     string,
     * }>
     */
    public function provideDataForTestTransform(): iterable
    {
        yield 'CrossContentType' => [
            new CrossContentType('All'),
            '{"type":"crossContentType","description":"All"}',
        ];

        yield 'OutputType Foo' => [
            new ItemType(1, 'Foo', [1]),
            '{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}',
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     string,
     *     \Ibexa\Personalization\Value\Content\AbstractItemType,
     * }>
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield 'CrossContentType' => [
            '{"type":"crossContentType","description":"All"}',
            new CrossContentType('All'),
        ];

        yield 'OutputType Foo' => [
            '{"type":"itemType","id":1,"contentTypes":["1"],"description":"Foo"}',
            new ItemType(1, 'Foo', [1]),
        ];
    }

    public function mockSerializerSerialize(AbstractItemType $value, string $json): void
    {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('serialize')
            ->with($value, 'json')
            ->willReturn($json);
    }

    public function mockSerializerDeserialize(string $value, AbstractItemType $itemType): void
    {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('deserialize')
            ->with($value, AbstractItemType::class, 'json')
            ->willReturn($itemType);
    }
}
