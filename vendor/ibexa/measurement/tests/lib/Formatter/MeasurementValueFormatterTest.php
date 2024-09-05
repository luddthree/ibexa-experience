<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\Formatter;

use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\Formatter\MeasurementValueFormatter;
use Ibexa\Measurement\Formatter\Strategy\FormattingStrategyInterface;
use PHPUnit\Framework\TestCase;

final class MeasurementValueFormatterTest extends TestCase
{
    private const EXAMPLE_FORMATTED_VALUE = '12.56g';

    public function testFormatUsingDefaultStrategy(): void
    {
        $value = $this->createMock(ValueInterface::class);

        $default = $this->createMock(FormattingStrategyInterface::class);
        $default->expects($this->once())->method('format')->with($value)->willReturn(self::EXAMPLE_FORMATTED_VALUE);

        $formatter = new MeasurementValueFormatter([
            'foo' => $this->createMock(FormattingStrategyInterface::class),
            'bar' => $this->createMock(FormattingStrategyInterface::class),
            'baz' => $default,
        ], $default);

        self::assertEquals(self::EXAMPLE_FORMATTED_VALUE, $formatter->format($value));
    }

    public function testFormatUsingSelectedStrategy(): void
    {
        $value = $this->createMock(ValueInterface::class);

        $selected = $this->createMock(FormattingStrategyInterface::class);
        $selected
            ->expects($this->once())
            ->method('format')
            ->with($value)
            ->willReturn(self::EXAMPLE_FORMATTED_VALUE);

        $formatter = new MeasurementValueFormatter(
            [
                'foo' => $selected,
                'bar' => $this->createMock(FormattingStrategyInterface::class),
                'baz' => $this->createMock(FormattingStrategyInterface::class),
            ],
            $this->createMock(FormattingStrategyInterface::class)
        );

        self::assertEquals(self::EXAMPLE_FORMATTED_VALUE, $formatter->format($value, 'foo'));
    }
}
