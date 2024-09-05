<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Value;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;

/**
 * @internal type-hint {@see \Ibexa\Contracts\Measurement\Value\RangeValueInterface} instead.
 */
final class RangeValue extends AbstractValue implements RangeValueInterface
{
    private float $minValue;

    private float $maxValue;

    public function __construct(
        MeasurementInterface $measurement,
        UnitInterface $unit,
        float $minValue,
        float $maxValue
    ) {
        $this->measurement = $measurement;
        $this->unit = $unit;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
    }

    public function getMinValue(): float
    {
        return $this->minValue;
    }

    public function getMaxValue(): float
    {
        return $this->maxValue;
    }

    public function equals(?RangeValueInterface $value, float $epsilon = PHP_FLOAT_EPSILON): bool
    {
        return $value !== null
            && $this->measurement->equals($value->getMeasurement())
            && $this->unit->equals($value->getUnit())
            && abs($this->minValue - $value->getMinValue()) < $epsilon
            && abs($this->maxValue - $value->getMaxValue()) < $epsilon;
    }

    public function __toString(): string
    {
        return sprintf(
            '%s: %f - %f %s',
            $this->measurement,
            $this->minValue,
            $this->maxValue,
            $this->unit
        );
    }
}
