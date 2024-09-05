<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\FieldTypePage\Form\DataTransformer;

use Ibexa\FieldTypePage\Form\DataTransformer\NestedAttributeDataTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\FieldTypePage\Form\DataTransformer\NestedAttributeDataTransformer
 */
final class NestedAttributeDataTransformerTest extends TestCase
{
    /** @var \Ibexa\FieldTypePage\Form\DataTransformer\NestedAttributeDataTransformer */
    private $nestedAttributeDataTransformer;

    protected function setUp(): void
    {
        $attributes = [
            'foo' => [
                'name' => '',
                'type' => '',
            ],
            'bar' => [
                'name' => 'Bar',
                'type' => 'bar',
            ],
        ];
        $this->nestedAttributeDataTransformer = new NestedAttributeDataTransformer($attributes);
    }

    /**
     * @dataProvider provideDataForTestTransform
     *
     * @phpstan-param array{
     *      array{
     *          'attributes': array<array<string, array<string, scalar>>>
     *      }
     * } $expected
     *
     * @throws \JsonException
     */
    public function testTransform(array $expected, ?string $value): void
    {
        self::assertEquals(
            $expected,
            $this->nestedAttributeDataTransformer->transform($value)
        );
    }

    /**
     * @dataProvider provideDataForTestReverseTransform
     *
     * @phpstan-param array{
     *      array{
     *          'attributes': array<array<string, array<string, scalar>>>
     *      }
     * } $value
     *
     * @throws \JsonException
     */
    public function testReverseTransform(string $expected, array $value): void
    {
        self::assertEquals(
            $expected,
            $this->nestedAttributeDataTransformer->reverseTransform($value)
        );
    }

    /**
     * @phpstan-return iterable<array{
     *      array{
     *          'attributes': array<array<string, array<string, scalar>>>
     *      },
     *     ?string,
     * }>
     */
    public function provideDataForTestTransform(): iterable
    {
        $initialData = $this->getEmptyData();

        yield 'Empty data' => [
            $initialData,
            null,
        ];

        yield 'Form data' => [
            $this->getFormData(),
            $this->getJson(),
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *     string,
     *     array{
     *          'attributes': array<array<string, array<string, scalar>>>
     *     },
     * }>
     */
    public function provideDataForTestReverseTransform(): iterable
    {
        yield 'Empty data' => [
            '{"attributes":[{"foo":{"value":null},"bar":{"value":null}}]}',
            $this->getEmptyData(),
        ];

        yield 'Form data' => [
            $this->getJson(),
            $this->getFormData(),
        ];
    }

    private function getJson(): string
    {
        return '{"attributes":[{"foo":{"value":1},"bar":{"value":""}},{"foo":{"value":2},"bar":{"value":"Bar"}}]}';
    }

    /**
     * @phpstan-return array{
     *     'attributes': array<array<string, array<string, scalar>>>
     * }
     */
    private function getFormData(): array
    {
        return [
            'attributes' => [
                [
                    'foo' => ['value' => 1],
                    'bar' => ['value' => ''],
                ],
                [
                    'foo' => ['value' => 2],
                    'bar' => ['value' => 'Bar'],
                ],
            ],
        ];
    }

    /**
     * @phpstan-return array{
     *     'attributes': array<array<string, array>>
     * }
     */
    private function getEmptyData(): array
    {
        return [
            'attributes' => [
                [
                    'foo' => [
                        'value' => null,
                    ],
                    'bar' => [
                        'value' => null,
                    ],
                ],
            ],
        ];
    }
}
