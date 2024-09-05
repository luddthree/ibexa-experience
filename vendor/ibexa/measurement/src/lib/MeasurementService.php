<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\Measurement\ValueFactory\ValueFactoryInterface;

/**
 * @internal type-hint {@see \Ibexa\Contracts\Measurement\MeasurementServiceInterface} instead.
 */
final class MeasurementService implements MeasurementServiceInterface
{
    private ValueFactoryInterface $measurementValueFactory;

    private UnitConverterDispatcherInterface $unitConverterDispatcher;

    public function __construct(
        ValueFactoryInterface $measurementValueFactory,
        UnitConverterDispatcherInterface $unitConverterDispatcher
    ) {
        $this->measurementValueFactory = $measurementValueFactory;
        $this->unitConverterDispatcher = $unitConverterDispatcher;
    }

    public function convert(
        ValueInterface $sourceValue,
        UnitInterface $destinationUnit
    ): ValueInterface {
        return $this->unitConverterDispatcher->convert($sourceValue, $destinationUnit);
    }

    public function buildSimpleValue(
        string $typeName,
        float $value,
        string $unitName
    ): SimpleValueInterface {
        return $this->measurementValueFactory->createSimpleValue($typeName, $value, $unitName);
    }

    public function buildRangeValue(
        string $typeName,
        float $minValue,
        float $maxValue,
        string $unitName
    ): RangeValueInterface {
        return $this->measurementValueFactory->createRangeValue($typeName, $minValue, $maxValue, $unitName);
    }
}
