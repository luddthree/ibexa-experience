<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ProductCatalog\Form\Attribute\Formatter;

use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\ProductCatalog\Local\Attribute\ValueFormatterInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeInterface;

final class MeasurementRangeValueFormatter implements ValueFormatterInterface
{
    private MeasurementValueFormatterInterface $valueFormatter;

    public function __construct(MeasurementValueFormatterInterface $valueFormatter)
    {
        $this->valueFormatter = $valueFormatter;
    }

    public function formatValue(AttributeInterface $attribute, array $parameters = []): ?string
    {
        $value = $attribute->getValue();

        if ($value instanceof RangeValueInterface) {
            return $this->valueFormatter->format($value);
        }

        return null;
    }
}
