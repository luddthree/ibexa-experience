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
use Ibexa\ProductCatalog\NumberFormatter\NumberFormatterFactoryInterface;
use NumberFormatter;

final class PresentationFormattingStrategy implements FormattingStrategyInterface
{
    public const NAME = 'presentation';

    private NumberFormatterFactoryInterface $numberFormatterFactory;

    public function __construct(NumberFormatterFactoryInterface $numberFormatterFactory)
    {
        $this->numberFormatterFactory = $numberFormatterFactory;
    }

    public function format(ValueInterface $value): string
    {
        $numberFormatter = $this->numberFormatterFactory->createNumberFormatter();

        if ($value instanceof SimpleValueInterface) {
            return $this->formatSimpleValue($numberFormatter, $value);
        }

        if ($value instanceof RangeValueInterface) {
            return $this->formatRangeValue($numberFormatter, $value);
        }

        throw new InvalidArgumentException('$value', 'Unsupported value class: ' . get_class($value));
    }

    private function formatSimpleValue(NumberFormatter $formatter, SimpleValueInterface $value): string
    {
        return $formatter->format($value->getValue()) . ' ' . $value->getUnit()->getSymbol();
    }

    private function formatRangeValue(NumberFormatter $formatter, RangeValueInterface $value): string
    {
        return vsprintf(
            '%s - %s %s',
            [
                $formatter->format($value->getMinValue()),
                $formatter->format($value->getMaxValue()),
                $value->getUnit()->getSymbol(),
            ]
        );
    }
}
