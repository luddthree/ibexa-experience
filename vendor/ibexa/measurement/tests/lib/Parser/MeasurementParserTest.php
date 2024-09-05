<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\Parser;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Measurement\Parser\MeasurementParser;
use PHPUnit\Framework\TestCase;

final class MeasurementParserTest extends TestCase
{
    private const EXAMPLE_TYPE_NAME = 'mass';
    private const EXAMPLE_UNIT_IDENTIFIER = 'gram';
    private const EXAMPLE_UNIT_SYMBOL = 'g';
    private const EXAMPLE_SIMPLE_VALUE = 1.235;
    private const EXAMPLE_RANGE_MIN_VALUE = 1.000;
    private const EXAMPLE_RANGE_MAX_VALUE = 2.356;

    /** @var \Ibexa\Contracts\Measurement\MeasurementServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private MeasurementServiceInterface $measurementService;

    private MeasurementParser $parser;

    private MeasurementInterface $measurement;

    protected function setUp(): void
    {
        $this->measurementService = $this->createMock(MeasurementServiceInterface::class);
        $this->measurement = $this->createExampleMeasurement();

        $this->parser = new MeasurementParser($this->measurementService);
    }

    public function testParseSimpleValue(): void
    {
        $expectedValue = $this->createMock(SimpleValueInterface::class);

        $this->measurementService
            ->expects($this->once())
            ->method('buildSimpleValue')
            ->with(
                self::EXAMPLE_TYPE_NAME,
                self::EXAMPLE_SIMPLE_VALUE,
                self::EXAMPLE_UNIT_IDENTIFIER
            )
            ->willReturn($expectedValue);

        self::assertEquals($expectedValue, $this->parser->parse($this->measurement, '1.235g'));
    }

    public function testParseRangeValue(): void
    {
        $expectedValue = $this->createMock(RangeValueInterface::class);

        $this->measurementService
            ->expects($this->once())
            ->method('buildRangeValue')
            ->with(
                self::EXAMPLE_TYPE_NAME,
                self::EXAMPLE_RANGE_MIN_VALUE,
                self::EXAMPLE_RANGE_MAX_VALUE,
                self::EXAMPLE_UNIT_IDENTIFIER
            )
            ->willReturn($expectedValue);

        self::assertEquals($expectedValue, $this->parser->parse($this->measurement, '1.0...2.356g'));
    }

    public function testParseMalformedSimpleValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed simple value: "X"');

        $this->parser->parse($this->measurement, 'X');
    }

    public function testParseMalformedRangeValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Malformed range value: "X..."');

        $this->parser->parse($this->measurement, 'X...');
    }

    private function createExampleMeasurement(): MeasurementInterface
    {
        $unit = $this->createMock(UnitInterface::class);
        $unit->method('getIdentifier')->willReturn(self::EXAMPLE_UNIT_IDENTIFIER);
        $unit->method('getSymbol')->willReturn(self::EXAMPLE_UNIT_SYMBOL);

        $measurement = $this->createMock(MeasurementInterface::class);
        $measurement->method('getName')->willReturn(self::EXAMPLE_TYPE_NAME);
        $measurement->method('getUnitBySymbol')->with(self::EXAMPLE_UNIT_SYMBOL)->willReturn($unit);

        return $measurement;
    }
}
