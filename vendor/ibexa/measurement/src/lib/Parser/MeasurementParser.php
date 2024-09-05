<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\Parser;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Parser\MeasurementParserInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Measurement\MeasurementValueFormat;

final class MeasurementParser implements MeasurementParserInterface
{
    private MeasurementServiceInterface $measurementService;

    public function __construct(MeasurementServiceInterface $measurementService)
    {
        $this->measurementService = $measurementService;
    }

    public function parse(MeasurementInterface $measurement, string $input): ValueInterface
    {
        if (str_contains($input, MeasurementValueFormat::RANGE_VALUE_SEPARATOR)) {
            return $this->parseRangeValue($measurement, $input);
        }

        return $this->parseSimpleValue($measurement, $input);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function parseRangeValue(MeasurementInterface $measurement, string $input): RangeValueInterface
    {
        [$min, $max, $symbol] = sscanf($input, MeasurementValueFormat::RANGE_VALUE_FORMAT);
        if ($min !== null && $max !== null && $symbol !== null) {
            return $this->measurementService->buildRangeValue(
                $measurement->getName(),
                $min,
                $max,
                $measurement->getUnitBySymbol($symbol)->getIdentifier()
            );
        }

        throw new InvalidArgumentException('$input', 'Malformed range value: "' . $input . '"');
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    private function parseSimpleValue(MeasurementInterface $measurement, string $input): SimpleValueInterface
    {
        [$value, $symbol] = sscanf($input, MeasurementValueFormat::SIMPLE_VALUE_FORMAT);
        if ($value !== null && $symbol !== null) {
            return $this->measurementService->buildSimpleValue(
                $measurement->getName(),
                $value,
                $measurement->getUnitBySymbol($symbol)->getIdentifier()
            );
        }

        throw new InvalidArgumentException('$input', 'Malformed simple value: "' . $input . '"');
    }
}
