<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Repository\Attribute;

use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use Ibexa\ProductCatalog\Local\Repository\Attribute\NumericValueFormatter;
use Ibexa\ProductCatalog\NumberFormatter\NumberFormatterFactoryInterface;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

final class NumericValueFormatterTest extends TestCase
{
    private const EXAMPLE_VALUE = 1000.54;
    private const EXAMPLE_FORMATTED_VALUE = '1 000,54';
    private const EXAMPLE_LOCALE = 'pl';

    public function testFormatValue(): void
    {
        $numberFormatter = $this->createNumberFormatter(self::EXAMPLE_VALUE, self::EXAMPLE_FORMATTED_VALUE);

        $numberFormatterFactory = $this->createMock(NumberFormatterFactoryInterface::class);
        $numberFormatterFactory
            ->method('createNumberFormatter')
            ->willReturn($numberFormatter);

        $attribute = $this->createAttributeWithValue(self::EXAMPLE_VALUE);

        $formatter = new NumericValueFormatter($numberFormatterFactory);

        self::assertEquals(
            self::EXAMPLE_FORMATTED_VALUE,
            $formatter->formatValue($attribute)
        );
    }

    public function testFormatValueWithNull(): void
    {
        $formatter = new NumericValueFormatter(
            $this->createMock(NumberFormatterFactoryInterface::class)
        );

        self::assertNull($formatter->formatValue($this->createAttributeWithValue(null)));
    }

    public function testFormatValueWithForcedLocale(): void
    {
        $numberFormatter = $this->createNumberFormatter(self::EXAMPLE_VALUE, self::EXAMPLE_FORMATTED_VALUE);

        $numberFormatterFactory = $this->createMock(NumberFormatterFactoryInterface::class);
        $numberFormatterFactory
            ->method('createNumberFormatter')
            ->with(self::EXAMPLE_LOCALE)
            ->willReturn($numberFormatter);

        $formatter = new NumericValueFormatter($numberFormatterFactory);

        self::assertEquals(
            self::EXAMPLE_FORMATTED_VALUE,
            $formatter->formatValue(
                $this->createAttributeWithValue(self::EXAMPLE_VALUE),
                [
                    'locale' => self::EXAMPLE_LOCALE,
                ]
            )
        );
    }

    public function testFormatWithExplicitFormatter(): void
    {
        $numberFormatter = $this->createNumberFormatter(self::EXAMPLE_VALUE, self::EXAMPLE_FORMATTED_VALUE);

        $numberFormatterFactory = $this->createMock(NumberFormatterFactoryInterface::class);
        $numberFormatterFactory
            ->expects(self::never())
            ->method('createNumberFormatter');

        $formatter = new NumericValueFormatter($numberFormatterFactory);

        self::assertEquals(
            self::EXAMPLE_FORMATTED_VALUE,
            $formatter->formatValue(
                $this->createAttributeWithValue(self::EXAMPLE_VALUE),
                [
                    'formatter' => $numberFormatter,
                ]
            )
        );
    }

    /**
     * @param float|int|null $value
     */
    private function createAttributeWithValue($value): AttributeInterface
    {
        $attribute = $this->createMock(AttributeInterface::class);
        $attribute->method('getValue')->willReturn($value);

        return $attribute;
    }

    private function createNumberFormatter(float $value, string $formatterValue): NumberFormatter
    {
        $numberFormatter = $this->createMock(NumberFormatter::class);
        $numberFormatter
            ->method('format')
            ->with($value)
            ->willReturn($formatterValue);

        return $numberFormatter;
    }
}
