<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CreatedAtCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CreatedAtRange;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CreatedAtCriterionTransformerTest extends TestCase
{
    private CreatedAtCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new CreatedAtCriterionTransformer();
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param array<string, DateTimeInterface|null> $expected
     */
    public function testTransform(?CreatedAtRange $value, ?array $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        $min = new DateTime();
        $max = new DateTime();

        yield 'null' => [null, null];
        yield 'CreatedAtRange Criterion' => [
            new CreatedAtRange($min, $max),
            [
                'min' => $min,
                'max' => $max,
            ],
        ];
        yield 'CreatedAtRange Criterion min' => [
            new CreatedAtRange($min, null),
            [
                'min' => $min,
                'max' => null,
            ],
        ];
        yield 'CreatedAtRange Criterion max' => [
            new CreatedAtRange(null, $max),
            [
                'min' => null,
                'max' => $max,
            ],
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . CreatedAtRange::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     *
     * @param array{min:DateTimeInterface|null, max: DateTimeInterface|null}|null $value
     */
    public function testReverseTransform(?array $value, ?CreatedAtRange $expected): void
    {
        self::assertEquals(
            $expected,
            $this->transformer->reverseTransform($value)
        );
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestReverseTransformDataProvider(): iterable
    {
        $min = new DateTime('2022-07-18T15:03:01');
        $max = new DateTime('2022-07-20T10:25:27');
        $expectedMax = new DateTime('2022-07-20T23:59:59');

        yield 'null' => [null, null];
        yield 'min and max' => [
            [
                'min' => $min,
                'max' => $max,
            ],
            new CreatedAtRange($min, $expectedMax),
        ];
        yield 'min' => [
            [
                'min' => $min,
                'max' => null,
            ],
            new CreatedAtRange($min, null),
        ];
        yield 'max' => [
            [
                'min' => null,
                'max' => $max,
            ],
            new CreatedAtRange(null, $expectedMax),
        ];
        yield 'immutable max' => [
            [
                'min' => null,
                'max' => new DateTimeImmutable('2022-07-20T10:25:27'),
            ],
            new CreatedAtRange(null, $expectedMax),
        ];
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformWithInvalidInputDataProvider
     *
     * @param mixed $value
     */
    public function testReverseTransformWithInvalidInput($value): void
    {
        $this->expectException(TransformationFailedException::class);

        $this->transformer->reverseTransform($value);
    }

    /**
     * @return iterable<string,array{mixed}>
     */
    public function dataProviderForTestReverseTransformWithInvalidInputDataProvider(): iterable
    {
        yield 'integer' => [123456];
        yield 'bool' => [true];
        yield 'float' => [12.34];
        yield 'string' => ['element'];
        yield 'object' => [new stdClass()];
    }
}
