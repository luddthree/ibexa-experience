<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Measurement\UnitConverter\SimpleValueFormulaUnitConverter;

final class SimpleValueFormulaUnitConverterTest extends AbstractValueFormulaUnitConverterTest
{
    public function testConversion(): void
    {
        $convertedMock = $this->createMock(SimpleValueInterface::class);
        $measurementService = $this->createMock(MeasurementServiceInterface::class);
        $measurementService
            ->expects(self::once())
            ->method('buildSimpleValue')
            ->with(
                self::identicalTo(''),
                self::identicalTo(1_000_000.0),
                self::identicalTo('bar'),
            )
            ->willReturn($convertedMock);

        $converter = $this->createConverter($measurementService);

        $value = $this->getValueMock('foo', 1_000);
        $unit = $this->createUnitMock('bar');

        $result = $converter->convert($value, $unit);
        self::assertInstanceOf(SimpleValueInterface::class, $result);
        self::assertSame($convertedMock, $result);
    }

    protected function getValueMock(
        string $unitIdentifier,
        ?float $value = null
    ): SimpleValueInterface {
        $mock = $this->createMock(SimpleValueInterface::class);
        $mock
            ->method('getUnit')
            ->willReturn($this->createUnitMock($unitIdentifier));

        if ($value !== null) {
            $mock
                ->method('getValue')
                ->willReturn($value);
        }

        return $mock;
    }

    protected function getConverterClass(): string
    {
        return SimpleValueFormulaUnitConverter::class;
    }
}
