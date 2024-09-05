<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\ValueFactory;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Measurement\MeasurementTypeFactoryInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Measurement\Value\RangeValue;
use Ibexa\Measurement\Value\SimpleValue;
use Traversable;

final class ValueFactory implements ValueFactoryInterface
{
    private MeasurementTypeFactoryInterface $measurementTypeFactory;

    public function __construct(MeasurementTypeFactoryInterface $measurementTypeFactory)
    {
        $this->measurementTypeFactory = $measurementTypeFactory;
    }

    public function createSimpleValue(
        string $typeName,
        float $value,
        string $unitName
    ): SimpleValueInterface {
        $measurementType = $this->getType($typeName, $unitName);

        return new SimpleValue($measurementType, $measurementType->getUnit($unitName), $value);
    }

    public function createRangeValue(
        string $typeName,
        float $minValue,
        float $maxValue,
        string $unitName
    ): RangeValueInterface {
        $measurementType = $this->getType($typeName, $unitName);

        return new RangeValue(
            $measurementType,
            $measurementType->getUnit($unitName),
            $minValue,
            $maxValue
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private function getType(string $typeName, string $unitName): MeasurementInterface
    {
        if (!$this->measurementTypeFactory->hasType($typeName)) {
            $types = $this->measurementTypeFactory->getAvailableTypes();
            throw new InvalidArgumentException(
                '$type',
                sprintf(
                    "%s does not exist. Available types: '%s'",
                    $typeName,
                    implode("', '", $types),
                ),
            );
        }

        $measurementType = $this->measurementTypeFactory->buildType($typeName);
        if (!$measurementType->hasUnit($unitName)) {
            $units = $measurementType->getUnitList();

            if ($units instanceof Traversable) {
                $units = iterator_to_array($units);
            }

            $unitNames = array_keys($units);

            throw new InvalidArgumentException(
                '$unit',
                sprintf(
                    "%s for %s does not exist. Available units: '%s'",
                    $unitName,
                    $typeName,
                    implode("', '", $unitNames),
                ),
            );
        }

        return $measurementType;
    }
}
