<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Formatter\Strategy;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\MeasurementValueFormat;

final class InputFormattingStrategy implements FormattingStrategyInterface
{
    public const NAME = 'input';

    public function format(ValueInterface $value): string
    {
        if ($value instanceof RangeValueInterface) {
            return $this->formatRangeValue($value);
        }

        if ($value instanceof SimpleValueInterface) {
            return $this->formatSimpleValue($value);
        }

        throw new InvalidArgumentException('$value', 'Unsupported value class: ' . get_class($value));
    }

    private function formatSimpleValue(SimpleValueInterface $value): string
    {
        return sprintf(
            MeasurementValueFormat::SIMPLE_VALUE_FORMAT,
            $value->getValue(),
            $value->getUnit()->getSymbol()
        );
    }

    private function formatRangeValue(RangeValueInterface $value): string
    {
        return sprintf(
            MeasurementValueFormat::RANGE_VALUE_FORMAT,
            $value->getMinValue(),
            $value->getMaxValue(),
            $value->getUnit()->getSymbol()
        );
    }
}
