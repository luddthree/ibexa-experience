<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\TargetedCatalogCustomerGroupMapAttributeTransformer;
use Ibexa\ProductCatalog\FieldType\CustomerGroup\Value;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * @covers \Ibexa\Bundle\ProductCatalog\Form\DataTransformer\TargetedCatalogCustomerGroupMapAttributeTransformer
 */
final class TargetedCatalogCustomerGroupMapAttributeTransformerTest extends TestCase
{
    private TargetedCatalogCustomerGroupMapAttributeTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new TargetedCatalogCustomerGroupMapAttributeTransformer();
    }

    /**
     * @dataProvider provideDataForTestTransform
     *
     * @param ?array{
     *      matches?: array<
     *          array-key, array{
     *              customer_group: \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *              catalog: int
     *          }
     *      >
     * } $expected
     *
     * @throws \JsonException
     */
    public function testTransform(?array $expected, ?string $value): void
    {
        self::assertEquals(
            $expected,
            $this->transformer->transform($value)
        );
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     *
     * @param array{
     *      matches?: array<
     *          array-key, array{
     *              customer_group: \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *              catalog: int
     *          }
     *      >
     * } $value
     *
     * @throws \JsonException
     */
    public function testReverseTransform(string $expected, array $value): void
    {
        self::assertEquals(
            $expected,
            $this->transformer->reverseTransform($value)
        );
    }

    /**
     * @dataProvider provideDataForTestTransformThrowTransformationFailedException
     *
     * @param mixed $value
     *
     * @throws \JsonException
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
     *
     * @throws \JsonException
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
     * @return iterable<string, array{
     *      ?array{
     *          matches?: array<
     *              array-key, array{
     *                  customer_group: \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *                  catalog: int
     *              }
     *          >
     *      },
     *     null|string,
     *  }
     * >
     */
    public function provideDataForTestTransform(): iterable
    {
        yield 'null' => [
            null,
            null,
        ];

        yield 'empty' => [
            [],
            '[]',
        ];

        yield 'matched results' => [
            [
                'matches' => [
                    [
                        'customer_group' => new Value(2),
                        'catalog' => 2,
                    ],
                    [
                        'customer_group' => new Value(3),
                        'catalog' => 4,
                    ],
                ],
            ],
            '[{"customer_group":2,"catalog":2},{"customer_group":3,"catalog":4}]',
        ];
    }

    /**
     * @return iterable<string,
     *      array{
     *          string,
     *          array{
     *              matches?: array<
     *                  array-key, array{
     *                      customer_group: \Ibexa\ProductCatalog\FieldType\CustomerGroup\Value,
     *                      catalog: int
     *                  }
     *              >
     *          },
     *      }
     * >
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield 'empty' => [
            '[]',
            [],
        ];

        yield 'matched results' => [
            '[{"customer_group":2,"catalog":2},{"customer_group":3,"catalog":4}]',
            [
                'matches' => [
                    [
                        'customer_group' => new Value(2),
                        'catalog' => 2,
                    ],
                    [
                        'customer_group' => new Value(3),
                        'catalog' => 4,
                    ],
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

        yield 'Missing customer_group key' => [
            'Invalid data. Missing "customer_group" or "catalog" keys',
            '[{"foo":2,"catalog":2}]',
        ];

        yield 'Missing catalog key' => [
            'Invalid data. Missing "customer_group" or "catalog" keys',
            '[{"customer_group":2,"foo":2}]',
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

        yield 'Invalid data. Missing "matches" root key' => [
            'Invalid data. Missing "matches" root key',
            ['foo'],
        ];
    }
}
