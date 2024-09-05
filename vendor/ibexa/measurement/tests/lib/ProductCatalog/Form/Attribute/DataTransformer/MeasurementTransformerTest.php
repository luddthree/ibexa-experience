<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\ProductCatalog\Form\Attribute\DataTransformer;

use Exception;
use Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface;
use Ibexa\Contracts\Measurement\Parser\MeasurementParserInterface;
use Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\ProductCatalog\Form\Attribute\DataTransformer\MeasurementTransformer;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Form\Exception\TransformationFailedException;

final class MeasurementTransformerTest extends TestCase
{
    /** @var \Ibexa\Contracts\Measurement\Parser\MeasurementParserInterface|\PHPUnit\Framework\MockObject\MockObject */
    private MeasurementParserInterface $measurementParser;

    /** @var \Ibexa\Contracts\Measurement\Formatter\MeasurementValueFormatterInterface|\PHPUnit\Framework\MockObject\MockObject */
    private MeasurementValueFormatterInterface $measurementFormatter;

    /** @var \Ibexa\Contracts\Measurement\Value\Definition\MeasurementInterface|\PHPUnit\Framework\MockObject\MockObject */
    private MeasurementInterface $measurement;

    private MeasurementTransformer $transformer;

    protected function setUp(): void
    {
        $this->measurementParser = $this->createMock(MeasurementParserInterface::class);
        $this->measurementFormatter = $this->createMock(MeasurementValueFormatterInterface::class);
        $this->measurement = $this->createMock(MeasurementInterface::class);

        $this->transformer = new MeasurementTransformer(
            $this->measurementParser,
            $this->measurementFormatter,
            $this->measurement
        );
    }

    public function testTransformNull(): void
    {
        self::assertNull($this->transformer->transform(null));
    }

    public function testTransform(): void
    {
        $value = $this->createMock(SimpleValueInterface::class);
        $value->method('getValue')->willReturn(1.58);
        $value->method('getUnit')->willReturn($this->createUnitWithSymbol('U'));

        $expectedValue = '1.58U';

        $this->measurementFormatter
            ->expects($this->once())
            ->method('format')
            ->with($value)
            ->willReturn($expectedValue);

        self::assertEquals($expectedValue, $this->transformer->transform($value));
    }

    public function testTransformWithInvalidInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage("Expected a Ibexa\Contracts\Measurement\Value\ValueInterface object, received stdClass.");

        $this->transformer->transform(new stdClass());
    }

    public function testReverseTransformNull(): void
    {
        self::assertNull($this->transformer->reverseTransform(null));
    }

    public function testReverseTransformValue(): void
    {
        $expectedValue = $this->createMock(ValueInterface::class);

        $this->measurementParser
            ->expects($this->once())
            ->method('parse')
            ->with($this->measurement, '1.58U')
            ->willReturn($expectedValue);

        self::assertEquals($expectedValue, $this->transformer->reverseTransform('1.58U'));
    }

    public function testReverseTransformWithNonStringInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Invalid data, expected a string value');

        $this->transformer->reverseTransform(new stdClass());
    }

    public function testReverseTransformInvalidStringInput(): void
    {
        $this->expectException(TransformationFailedException::class);
        $this->expectExceptionMessage('Parser exception');

        $exception = new Exception('Parser exception');

        $input = 'invalid';

        $this->measurementParser
            ->expects($this->once())
            ->method('parse')
            ->with($this->measurement, $input)
            ->willThrowException($exception);

        $this->transformer->reverseTransform($input);
    }

    private function createUnitWithSymbol(string $symbol): UnitInterface
    {
        $unit = $this->createMock(UnitInterface::class);
        $unit->method('getSymbol')->willReturn($symbol);

        return $unit;
    }
}
