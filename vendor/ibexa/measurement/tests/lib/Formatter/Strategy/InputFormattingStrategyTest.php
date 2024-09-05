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
use Ibexa\Measurement\Formatter\Strategy\InputFormattingStrategy;
use PHPUnit\Framework\TestCase;

final class InputFormattingStrategyTest extends TestCase
{
    private InputFormattingStrategy $formattingStrategy;

    protected function setUp(): void
    {
        $this->formattingStrategy = new InputFormattingStrategy();
    }

    /**
     * @dataProvider dataProviderForTestFormat
     */
    public function testFormat(ValueInterface $value, string $expectedValue): void
    {
        self::assertEquals($expectedValue, $this->formattingStrategy->format($value));
    }

    /**
     * @return iterable<string,array{ValueInterface|null,string|null}>
     */
    public function dataProviderForTestFormat(): iterable
    {
        $simple = $this->createMock(SimpleValueInterface::class);
        $simple->method('getValue')->willReturn(1.58);
        $simple->method('getUnit')->willReturn($this->createUnitWithSymbol('U'));

        yield 'simple' => [
            $simple,
            '1.580000U',
        ];

        $range = $this->createMock(RangeValueInterface::class);
        $range->method('getMinValue')->willReturn(1.0);
        $range->method('getMaxValue')->willReturn(10.0);
        $range->method('getUnit')->willReturn($this->createUnitWithSymbol('U'));

        yield 'range' => [
            $range,
            '1.000000...10.000000U',
        ];
    }

    public function testFormatWithUnsupportedValue(): void
    {
        $this->expectExceptionMessage('Unsupported value class: ');
        $this->expectException(InvalidArgumentException::class);

        $this->formattingStrategy->format($this->createMock(ValueInterface::class));
    }

    private function createUnitWithSymbol(string $symbol): UnitInterface
    {
        $unit = $this->createMock(UnitInterface::class);
        $unit->method('getSymbol')->willReturn($symbol);

        return $unit;
    }
}
