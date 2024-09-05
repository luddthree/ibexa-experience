<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Converter\UnitConverterInterface;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

abstract class AbstractValueFormulaUnitConverterTest extends TestCase
{
    public function testSupports(): void
    {
        $measurementService = $this->createMock(MeasurementServiceInterface::class);
        $converter = $this->createConverter($measurementService);

        self::assertFalse(
            $converter->supports(
                $this->createMock(ValueInterface::class),
                $this->createMock(UnitInterface::class)
            )
        );

        self::assertFalse(
            $converter->supports(
                $this->getValueMock('foo'),
                $this->createUnitMock('non_existent')
            )
        );

        self::assertTrue($converter->supports(
            $this->getValueMock('foo'),
            $this->createUnitMock('bar')
        ));
    }

    final protected function createConverter(MeasurementServiceInterface $measurementService): UnitConverterInterface
    {
        $converterClass = $this->getConverterClass();

        return new $converterClass(
            $measurementService,
            new ExpressionLanguage(),
            [
                ['source_unit' => 'foo', 'target_unit' => 'bar', 'formula' => 'value * 1000'],
            ],
        );
    }

    final protected function createUnitMock(string $identifier): UnitInterface
    {
        $unit = $this->createMock(UnitInterface::class);
        $unit
            ->method('getIdentifier')
            ->willReturn($identifier);

        return $unit;
    }

    abstract protected function getValueMock(string $unitIdentifier): ValueInterface;

    /**
     * @return class-string<\Ibexa\Contracts\Measurement\Converter\UnitConverterInterface>
     */
    abstract protected function getConverterClass(): string;
}
