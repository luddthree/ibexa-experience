<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ColorAttributeCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ColorAttribute;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class ColorAttributeCriterionTransformerTest extends TestCase
{
    private const IDENTIFIER = 'foo';
    private const COLOR = '#ffffff';

    private ColorAttributeCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new ColorAttributeCriterionTransformer(self::IDENTIFIER);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param string[]|null $expected
     */
    public function testTransform(?ColorAttribute $value, ?array $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'Color Attribute Criterion' => [
            new ColorAttribute(self::IDENTIFIER, [self::COLOR]),
            [self::COLOR],
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . ColorAttribute::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        self::assertEquals(
            new ColorAttribute(self::IDENTIFIER, [self::COLOR]),
            $this->transformer->reverseTransform([self::COLOR])
        );
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
        yield 'string' => ['foo'];
        yield 'object' => [new stdClass()];
    }
}
