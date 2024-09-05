<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\Formatter\Strategy;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\Formatter\Strategy\PresentationFormattingStrategy;
use Ibexa\ProductCatalog\NumberFormatter\NumberFormatterFactoryInterface;
use NumberFormatter;
use PHPUnit\Framework\TestCase;

final class PresentationFormattingStrategyTest extends TestCase
{
    private PresentationFormattingStrategy $formattingStrategy;

    protected function setUp(): void
    {
        $numberFormatter = $this->createMock(NumberFormatter::class);
        $numberFormatter
            ->method('format')
            ->willReturnCallback(static fn (float $value) => (string)$value);

        $numberFormatterFactory = $this->createMock(NumberFormatterFactoryInterface::class);
        $numberFormatterFactory
            ->method('createNumberFormatter')
            ->willReturn($numberFormatter);

        $this->formattingStrategy = new PresentationFormattingStrategy($numberFormatterFactory);
    }

    /**
     * @dataProvider dataProviderForTestFormat
     */
    public function testFormat(ValueInterface $value, string $expectedValue): void
    {
        self::assertEquals($expectedValue, $this->formattingStrategy->format($value));
    }

    public function testFormatWithUnsupportedValue(): void
    {
        $this->expectExceptionMessage('Unsupported value class: ');
        $this->expectException(InvalidArgumentException::class);

        $this->formattingStrategy->format($this->createMock(ValueInterface::class));
    }

    /**
     * @return iterable<string,array{ValueInterface|null,string|null}>
     */
    public function dataProviderForTestFormat(): iterable
    {
        yield 'simple' => [
            $this->createSimpleValue(36.6, '°C'),
            '36.6 °C',
        ];

        yield 'range' => [
            $this->createRangeValue(-100.0, 100.0, 'U'),
            '-100 - 100 U',
        ];
    }

    private function createSimpleValue(float $value, string $unit): SimpleValueInterface
    {
        $measurement = $this->createMock(SimpleValueInterface::class);
        $measurement->method('getValue')->willReturn($value);
        $measurement->method('getUnit')->willReturn($this->createUnitWithSymbol($unit));

        return $measurement;
    }

    private function createRangeValue(float $min, float $max, string $unit): RangeValueInterface
    {
        $measurement = $this->createMock(RangeValueInterface::class);
        $measurement->method('getMinValue')->willReturn($min);
        $measurement->method('getMaxValue')->willReturn($max);
        $measurement->method('getUnit')->willReturn($this->createUnitWithSymbol($unit));

        return $measurement;
    }

    private function createUnitWithSymbol(string $symbol): UnitInterface
    {
        $unit = $this->createMock(UnitInterface::class);
        $unit->method('getSymbol')->willReturn($symbol);

        return $unit;
    }
}
