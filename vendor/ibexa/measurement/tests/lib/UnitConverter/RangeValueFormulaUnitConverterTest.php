<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Measurement\UnitConverter\RangeValueFormulaUnitConverter;

final class RangeValueFormulaUnitConverterTest extends AbstractValueFormulaUnitConverterTest
{
    public function testConversion(): void
    {
        $convertedMock = $this->createMock(RangeValueInterface::class);
        $measurementService = $this->createMock(MeasurementServiceInterface::class);
        $measurementService
            ->expects(self::once())
            ->method('buildRangeValue')
            ->with(
                self::identicalTo(''),
                self::identicalTo(1_000_000.0),
                self::identicalTo(2_000_000.0),
                self::identicalTo('bar'),
            )
            ->willReturn($convertedMock);

        $converter = $this->createConverter($measurementService);

        $value = $this->getValueMock('foo', 1_000, 2_000);
        $unit = $this->createUnitMock('bar');

        $result = $converter->convert($value, $unit);
        self::assertInstanceOf(RangeValueInterface::class, $result);
        self::assertSame($convertedMock, $result);
    }

    protected function getValueMock(
        string $unitIdentifier,
        ?float $minValue = null,
        ?float $maxValue = null
    ): RangeValueInterface {
        $value = $this->createMock(RangeValueInterface::class);
        $value
            ->method('getUnit')
            ->willReturn($this->createUnitMock($unitIdentifier));

        if ($minValue !== null) {
            $value
                ->method('getMinValue')
                ->willReturn($minValue);
        }

        if ($maxValue !== null) {
            $value
                ->method('getMaxValue')
                ->willReturn($maxValue);
        }

        return $value;
    }

    protected function getConverterClass(): string
    {
        return RangeValueFormulaUnitConverter::class;
    }
}
