<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Formatter;

use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;
use LogicException;

final class MeasurementValueFormatter implements ValueFormatterInterface
{
    private MeasurementRangeValueFormatter $rangeValueFormatter;

    private MeasurementSingleValueFormatter $singleValueFormatter;

    public function __construct(
        MeasurementRangeValueFormatter $rangeValueFormatter,
        MeasurementSingleValueFormatter $singleValueFormatter
    ) {
        $this->rangeValueFormatter = $rangeValueFormatter;
        $this->singleValueFormatter = $singleValueFormatter;
    }

    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $value = $attribute->getValue();
        if ($value === null) {
            return null;
        }

        if ($value instanceof RangeValueInterface) {
            return $this->rangeValueFormatter->formatValue($attribute);
        } elseif ($value instanceof SimpleValueInterface) {
            return $this->singleValueFormatter->formatValue($attribute);
        }

        throw new LogicException(sprintf(
            'Unable to format value for object of class "%s". Expected one of: "%s"',
            get_debug_type($value),
            implode('", "', [RangeValueInterface::class, SimpleValueInterface::class]),
        ));
    }
}
