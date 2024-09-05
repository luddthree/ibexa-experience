<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcher;
use Ibexa\Measurement\UnitConverter\UnitConverterDispatcherInterface;
use Ibexa\Measurement\Value\RangeValue;
use Ibexa\Measurement\Value\SimpleValue;

/**
 * @covers \Ibexa\Measurement\UnitConverter\UnitConverterDispatcher
 */
final class UnitConverterDispatcherTest extends IbexaKernelTestCase
{
    private UnitConverterDispatcherInterface $converter;

    protected function setUp(): void
    {
        $this->converter = $this->getIbexaTestCore()->getServiceByClassName(UnitConverterDispatcher::class);
    }

    public function testConvertSimpleValueUsingCustomUnits(): void
    {
        $unitSource = $this->createUnitMock('foo');
        $unitTarget = $this->createUnitMock('bar');
        $type = $this->createTypeMock('area');
        $value = new SimpleValue(
            $type,
            $unitSource,
            0.1,
        );

        $newValue = $this->converter->convert($value, $unitTarget);
        self::assertInstanceOf(SimpleValue::class, $newValue);
        self::assertEqualsWithDelta(0.001, $newValue->getValue(), 0.001);
    }

    public function testConvertRangeValueUsingCustomUnits(): void
    {
        $unitSource = $this->createUnitMock('foo');
        $unitTarget = $this->createUnitMock('bar');
        $type = $this->createTypeMock('area');
        $value = new RangeValue(
            $type,
            $unitSource,
            0.1,
            0.7,
        );

        $newValue = $this->converter->convert($value, $unitTarget);
        self::assertInstanceOf(RangeValue::class, $newValue);
        self::assertEqualsWithDelta(0.001, $newValue->getMinValue(), 0.001);
        self::assertEqualsWithDelta(0.007, $newValue->getMaxValue(), 0.001);
    }

    public function testConvertSimpleValueUsingBuiltInUnits(): void
    {
        $unitSource = $this->createUnitMock('hectare');
        $unitTarget = $this->createUnitMock('square meter');
        $type = $this->createTypeMock('area');
        $value = new SimpleValue(
            $type,
            $unitSource,
            0.1,
        );

        $newValue = $this->converter->convert($value, $unitTarget);
        self::assertInstanceOf(SimpleValue::class, $newValue);
        self::assertEqualsWithDelta(1_000, $newValue->getValue(), 0.001);
    }

    public function testConvertRangeValueUsingBuiltInUnits(): void
    {
        $unitSource = $this->createUnitMock('hectare');
        $unitTarget = $this->createUnitMock('square meter');
        $type = $this->createTypeMock('area');
        $value = new RangeValue(
            $type,
            $unitSource,
            0.1,
            0.7,
        );

        $newValue = $this->converter->convert($value, $unitTarget);
        self::assertInstanceOf(RangeValue::class, $newValue);
        self::assertEqualsWithDelta(1_000, $newValue->getMinValue(), 0.001);
        self::assertEqualsWithDelta(7_000, $newValue->getMaxValue(), 0.001);
    }

    private function createUnitMock(string $identifier): UnitInterface
    {
        $unitSource = $this->createMock(UnitInterface::class);
        $unitSource->method('getIdentifier')->willReturn($identifier);

        return $unitSource;
    }

    private function createTypeMock(string $name): MeasurementInterface
    {
        $type = $this->createMock(MeasurementInterface::class);
        $type->method('getName')->willReturn($name);

        return $type;
    }
}
