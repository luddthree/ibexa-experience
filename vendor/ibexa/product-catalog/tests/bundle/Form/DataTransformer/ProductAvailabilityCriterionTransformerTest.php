<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductAvailabilityCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductAvailability;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ProductAvailabilityCriterionTransformerTest extends TestCase
{
    private ProductAvailabilityCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ProductAvailabilityCriterionTransformer();
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?ProductAvailability $value, ?bool $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'available' => [new ProductAvailability(true), true];
        yield 'unavailable' => [new ProductAvailability(false), false];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Expected a ' . ProductAvailability::class . ' object.');

        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     */
    public function testReverseTransform(?bool $value, ?ProductAvailability $expected): void
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
        yield 'null' => [null, null];
        yield 'available' => [true, new ProductAvailability(true)];
        yield 'unavailable' => [false, new ProductAvailability(false)];
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
        yield 'integer' => [123457];
        yield 'string' => ['foo'];
        yield 'float' => [12.34];
        yield 'array' => [['element']];
        yield 'object' => [new stdClass()];
    }
}
