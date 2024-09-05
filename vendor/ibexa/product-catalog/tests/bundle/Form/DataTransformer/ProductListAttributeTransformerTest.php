<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductListAttributeTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductListAttributeTransformer
 */
final class ProductListAttributeTransformerTest extends TestCase
{
    private ProductListAttributeTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ProductListAttributeTransformer();
    }

    /**
     * @dataProvider provideDataForTestTransform
     *
     * @param ?array{codes?: array<string>} $expected
     */
    public function testTransform(
        ?array $expected,
        ?string $value
    ): void {
        self::assertEquals(
            $expected,
            $this->transformer->transform($value)
        );
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     *
     * @param ?array{codes: array<string>} $value
     */
    public function testReverseTransform(
        ?string $expected,
        ?array $value
    ): void {
        self::assertEquals(
            $expected,
            $this->transformer->reverseTransform($value)
        );
    }

    /**
     * @dataProvider provideDataForTestTransformThrowTransformationFailedException
     *
     * @param mixed $value
     */
    public function testTransformThrowTransformationFailedException(
        string $expectedExceptionMessage,
        $value
    ): void {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->transformer->transform($value);
    }

    /**
     * @dataProvider provideDataForTestReverseTransformThrowTransformationFailedException
     *
     * @param mixed $value
     */
    public function testReverseTransformThrowTransformationFailedException(
        string $expectedExceptionMessage,
        $value
    ): void {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $this->transformer->reverseTransform($value);
    }

    /**
     * @return iterable<array{
     *      ?array{codes?: array<string>},
     *      ?string
     * }>
     */
    public function provideDataForTestTransform(): iterable
    {
        yield 'null' => [
            null,
            null,
        ];

        yield 'Product codes' => [
            [
                'codes' => [
                    'foo_123456',
                    'bar_1357',
                    'baz_97531',
                ],
            ],
            'foo_123456,bar_1357,baz_97531',
        ];
    }

    /**
     * @return iterable<array{
     *      ?string,
     *      ?array{codes: array<string>},
     * }>
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield 'null' => [
            null,
            null,
        ];

        yield 'Product codes' => [
            'foo_123456,bar_1357,baz_97531',
            [
                'codes' => [
                    'foo_123456',
                    'bar_1357',
                    'baz_97531',
                ],
            ],
        ];
    }

    /**
     * @return iterable<array{
     *     string,
     *     int|string
     * }>
     */
    public function provideDataForTestTransformThrowTransformationFailedException(): iterable
    {
        yield 'Value other than string' => [
            'Invalid data, string value expected',
            12345,
        ];
    }

    /**
     * @return iterable<array{
     *     string,
     *     int|array<mixed>
     * }>
     */
    public function provideDataForTestReverseTransformThrowTransformationFailedException(): iterable
    {
        yield 'Value other than array' => [
            'Invalid data, array value expected',
            12345,
        ];

        yield 'Invalid data. Missing "codes" key' => [
            'Invalid data. Missing "codes" key',
            ['foo'],
        ];
    }
}
