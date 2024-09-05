<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Value;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;

/**
 * @internal type-hint {@see \Ibexa\Contracts\Measurement\Value\SimpleValueInterface} instead.
 */
final class SimpleValue extends AbstractValue implements SimpleValueInterface, \JsonSerializable
{
    private float $value;

    public function __construct(MeasurementInterface $measurement, UnitInterface $unit, float $value)
    {
        $this->measurement = $measurement;
        $this->unit = $unit;
        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function equals(?SimpleValueInterface $value, float $epsilon = PHP_FLOAT_EPSILON): bool
    {
        return $value !== null
            && $this->measurement->equals($value->getMeasurement())
            && $this->unit->equals($value->getUnit())
            && abs($this->value - $value->getValue()) < $epsilon;
    }

    public function __toString(): string
    {
        return sprintf('%s %f %s', $this->measurement, $this->value, $this->unit);
    }

    /**
     * @return array<string,mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'measurementUnit' => $this->unit->getIdentifier(),
        ];
    }
}
