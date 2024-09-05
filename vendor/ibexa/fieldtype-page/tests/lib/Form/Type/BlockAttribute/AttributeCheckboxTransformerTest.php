<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\FieldTypePage\Form\Type\BlockAttribute\AttributeCheckboxTransformer;
use PHPUnit\Framework\TestCase;

final class AttributeCheckboxTransformerTest extends TestCase
{
    /**
     * @param string|bool|null $value
     *
     * @dataProvider provideForTransform
     */
    public function testTransform($value, ?bool $expectedValue): void
    {
        $transformer = new AttributeCheckboxTransformer();
        $transformerValue = $transformer->transform($value);
        self::assertSame($expectedValue, $transformerValue);
    }

    /**
     * @return iterable<array{string|bool|null, bool|null}>
     */
    public static function provideForTransform(): iterable
    {
        yield [null, null];

        yield ['1', true];
        yield [true, true];

        yield ['0', false];
        yield [false, false];
    }

    /**
     * @param string|bool|null $value
     *
     * @dataProvider provideForReverseTransform
     */
    public function testReverseTransform($value, ?string $expectedValue): void
    {
        $transformer = new AttributeCheckboxTransformer();
        $transformerValue = $transformer->reverseTransform($value);
        self::assertSame($expectedValue, $transformerValue);
    }

    /**
     * @return iterable<array{string|bool|null, string|null}>
     */
    public static function provideForReverseTransform(): iterable
    {
        yield [null, null];

        yield ['1', '1'];
        yield [true, '1'];

        yield ['0', '0'];
        yield [false, '0'];
    }
}
