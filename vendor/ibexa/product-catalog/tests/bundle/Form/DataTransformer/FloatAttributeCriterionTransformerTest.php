<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\Form\DataTransformer;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\FloatAttributeCriterionTransformer;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\FloatAttributeRange;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class FloatAttributeCriterionTransformerTest extends TestCase
{
    private const IDENTIFIER = 'foo';
    private const MIN = 0.1;
    private const MAX = 0.9;

    private FloatAttributeCriterionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new FloatAttributeCriterionTransformer(self::IDENTIFIER);
    }

    /**
     * @dataProvider dataProviderForTestTransformDataProvider
     *
     * @param array<string, float|null>|null $expected
     */
    public function testTransform(?FloatAttributeRange $value, ?array $expected): void
    {
        self::assertEquals($expected, $this->transformer->transform($value));
    }

    /**
     * @return iterable<string,mixed>
     */
    public function dataProviderForTestTransformDataProvider(): iterable
    {
        yield 'null' => [null, null];
        yield 'Range' => [
            new FloatAttributeRange(self::IDENTIFIER, self::MIN, self::MAX),
            ['min' => self::MIN, 'max' => self::MAX],
        ];
        yield 'Min' => [
            new FloatAttributeRange(self::IDENTIFIER, self::MIN, null),
            ['min' => self::MIN, 'max' => null],
        ];
        yield 'Max' => [
            new FloatAttributeRange(self::IDENTIFIER, null, self::MAX),
            ['min' => null, 'max' => self::MAX],
        ];
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage(
            'Expected a ' . FloatAttributeRange::class . ' object, received ' . stdClass::class . '.'
        );

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransform(): void
    {
        self::assertEquals(
            new FloatAttributeRange(self::IDENTIFIER, self::MIN, self::MAX),
            $this->transformer->reverseTransform(['min' => self::MIN, 'max' => self::MAX])
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
