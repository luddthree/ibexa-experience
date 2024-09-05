<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Measurement;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

interface MeasurementServiceInterface
{
    /**
     * @template T of \Ibexa\Contracts\Measurement\Value\ValueInterface
     *
     * @param T $sourceValue
     *
     * @return T
     *
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException if conversion for a given input is not supported
     */
    public function convert(
        ValueInterface $sourceValue,
        UnitInterface $destinationUnit
    ): ValueInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function buildSimpleValue(
        string $typeName,
        float $value,
        string $unitName
    ): SimpleValueInterface;

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function buildRangeValue(
        string $typeName,
        float $minValue,
        float $maxValue,
        string $unitName
    ): RangeValueInterface;
}
