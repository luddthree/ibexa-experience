<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class StringToArrayTransformerTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer<mixed> $transformer
     * @param mixed $value
     */
    public function testTransform(StringToArrayTransformer $transformer, $value, ?string $expected): void
    {
        self::assertEquals($expected, $transformer->transform($value));
    }

    /**
     * @return iterable<array-key, array{StringToArrayTransformer<mixed>,string[]|null,string|null}>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield [
            new StringToArrayTransformer(),
            null,
            null,
        ];

        yield [
            new StringToArrayTransformer(),
            [],
            '',
        ];

        yield [
            new StringToArrayTransformer(),
            ['foo', 'bar', 'baz'],
            'foo,bar,baz',
        ];

        yield [
            new StringToArrayTransformer(null, ';'),
            ['foo', 'bar', 'baz'],
            'foo;bar;baz',
        ];

        yield [
            new StringToArrayTransformer($this->getStringCaseTransformer()),
            ['foo', 'bar', 'baz'],
            'FOO,BAR,BAZ',
        ];
    }

    /**
     * @dataProvider dataProviderForTestTransformWithInvalidInput
     *
     * @param \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer<mixed> $transformer
     * @param mixed $value
     */
    public function testTransformWithInvalidInput(
        StringToArrayTransformer $transformer,
        $value,
        string $expectedMessage
    ): void {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $transformer->transform($value);
    }

    /**
     * @return iterable<array-key, array{StringToArrayTransformer<mixed>,mixed,string}>
     */
    public function dataProviderForTestTransformWithInvalidInput(): iterable
    {
        yield [
            new StringToArrayTransformer(),
            new stdClass(),
            'Invalid data, expected a array or null value',
        ];

        yield [
            new StringToArrayTransformer($this->getFailingDataTransformer()),
            ['foo', 'bar', 'baz'],
            'transformation failed',
        ];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransform
     *
     * @param \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer<mixed> $transformer
     * @param mixed $value
     * @param array<mixed>|null $expected
     */
    public function testReverseTransform(StringToArrayTransformer $transformer, $value, ?array $expected): void
    {
        self::assertEquals($expected, $transformer->reverseTransform($value));
    }

    /**
     * @return iterable<array-key, array{StringToArrayTransformer<mixed>,string|null,string[]|null}>
     */
    public function dataProviderForTestReverseTransform(): iterable
    {
        yield [
            new StringToArrayTransformer(),
            null,
            null,
        ];

        yield [
            new StringToArrayTransformer(),
            '',
            null,
        ];

        yield [
            new StringToArrayTransformer(),
            '   ',
            null,
        ];

        yield [
            new StringToArrayTransformer(),
            'foo, bar, baz',
            ['foo', 'bar', 'baz'],
        ];

        yield [
            new StringToArrayTransformer(null, ';'),
            'foo;bar;baz',
            ['foo', 'bar', 'baz'],
        ];

        yield [
            new StringToArrayTransformer($this->getStringCaseTransformer()),
            'FOO,BAR,BAZ',
            ['foo', 'bar', 'baz'],
        ];

        yield [
            new StringToArrayTransformer($this->getStringCaseTransformer()),
            'FOO,BAR,,BAZ',
            ['foo', 'bar', 'baz'],
        ];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformWithInvalidInput
     *
     * @param \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\StringToArrayTransformer<mixed> $transformer
     * @param mixed $value
     */
    public function testReverseTransformWithInvalidInput(
        StringToArrayTransformer $transformer,
        $value,
        string $expectedMessage
    ): void {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedMessage);

        $transformer->reverseTransform($value);
    }

    /**
     * @return iterable<array-key, array{StringToArrayTransformer<mixed>,mixed,string}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInput(): iterable
    {
        yield [
            new StringToArrayTransformer(),
            new stdClass(),
            'Invalid data, expected a string or null value',
        ];

        yield [
            new StringToArrayTransformer($this->getFailingDataTransformer()),
            'foo, bar, baz',
            'reverse transformation failed',
        ];
    }

    /**
     * @return \Symfony\Component\Form\DataTransformerInterface<mixed,mixed>
     */
    private function getFailingDataTransformer(): DataTransformerInterface
    {
        $transformer = $this->createMock(DataTransformerInterface::class);
        $transformer
            ->method('transform')
            ->willThrowException(new TransformationFailedException('transformation failed'));

        $transformer
            ->method('reverseTransform')
            ->willThrowException(new TransformationFailedException('reverse transformation failed'));

        return $transformer;
    }

    /**
     * @return \Symfony\Component\Form\DataTransformerInterface<mixed,int|string>
     */
    private function getStringCaseTransformer(): DataTransformerInterface
    {
        return new class() implements DataTransformerInterface {
            public function transform($value): ?string
            {
                if ($value === null) {
                    return null;
                }

                return strtoupper($value);
            }

            public function reverseTransform($value): ?string
            {
                if ($value === null) {
                    return null;
                }

                return strtolower($value);
            }
        };
    }
}
