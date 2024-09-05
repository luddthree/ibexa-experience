<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\CheckboxAttributeCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\CheckboxAttribute;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class CheckboxAttributeCriterionTransformerTest extends TestCase
{
    private const IDENTIFIER = 'foo';

    private CheckboxAttributeCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new CheckboxAttributeCriterionTransformer(self::IDENTIFIER);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     */
    public function testTransform(?CheckboxAttribute $value, ?bool $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'selected' => [new CheckboxAttribute(self::IDENTIFIER, true), true];
        yield 'unselected' => [new CheckboxAttribute(self::IDENTIFIER, false), false];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . CheckboxAttribute::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    /**
     * @dataProvider dataProviderForTestReverseTransformDataProvider
     */
    public function testReverseTransform(?bool $value, ?CheckboxAttribute $expected): void
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
        yield 'selected' => [true, new CheckboxAttribute(self::IDENTIFIER, true)];
        yield 'unselected' => [false, new CheckboxAttribute(self::IDENTIFIER, false)];
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
