<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\FieldType\MeasurementType;

final class RangeMeasurementFieldValueProcessor extends AbstractMeasurementFieldValueProcessor
{
    protected function getValueType(): int
    {
        return MeasurementType::INPUT_TYPE_RANGE;
    }

    /**
     * @param \Ibexa\Contracts\Measurement\Value\RangeValueInterface|null $measurementValue
     */
    protected function normalizeMeasurementValue(?ValueInterface $measurementValue): array
    {
        return [
            'value_range_min' => null !== $measurementValue
                ? $measurementValue->getMinValue()
                : null,
            'value_range_max' => $measurementValue
                ? $measurementValue->getMaxValue()
                : null,
        ];
    }
}
