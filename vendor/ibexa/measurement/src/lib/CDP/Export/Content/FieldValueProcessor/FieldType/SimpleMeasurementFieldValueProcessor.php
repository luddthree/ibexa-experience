<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\CDP\Export\Content\FieldValueProcessor\FieldType;

use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\FieldType\MeasurementType;

final class SimpleMeasurementFieldValueProcessor extends AbstractMeasurementFieldValueProcessor
{
    protected function getValueType(): int
    {
        return MeasurementType::INPUT_TYPE_SIMPLE_INPUT;
    }

    /**
     * @param \Ibexa\Contracts\Measurement\Value\SimpleValueInterface|null $measurementValue
     */
    protected function normalizeMeasurementValue(?ValueInterface $measurementValue): array
    {
        return [
            'value_simple' => null !== $measurementValue
                ? $measurementValue->getValue()
                : null,
        ];
    }
}
