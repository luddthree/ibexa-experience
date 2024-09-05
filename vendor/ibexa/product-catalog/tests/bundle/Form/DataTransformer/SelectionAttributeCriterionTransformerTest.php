<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\SelectionAttributeCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\SelectionAttribute;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class SelectionAttributeCriterionTransformerTest extends TestCase
{
    private const IDENTIFIER = 'foo';
    private const VALUE = 'value';

    private SelectionAttributeCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new SelectionAttributeCriterionTransformer(self::IDENTIFIER);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param string[]|null $expected
     */
    public function testTransform(?SelectionAttribute $value, ?array $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'Selection Attribute Criterion' => [
            new SelectionAttribute(self::IDENTIFIER, [self::VALUE]),
            [self::VALUE],
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . SelectionAttribute::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        self::assertEquals(
            new SelectionAttribute(self::IDENTIFIER, [self::VALUE]),
            $this->transformer->reverseTransform([self::VALUE])
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
        yield 'string' => ['element'];
        yield 'object' => [new stdClass()];
    }
}
