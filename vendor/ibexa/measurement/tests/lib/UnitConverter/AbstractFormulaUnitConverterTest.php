<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\UnitConverter\AbstractFormulaUnitConverter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class AbstractFormulaUnitConverterTest extends TestCase
{
    /**
     * @dataProvider provideForInstantiationFailure
     *
     * @param non-empty-array<mixed> $formulas
     */
    public function testInstantiationFailsWithInvalidFormula(array $formulas, string $expectedMissingField): void
    {
        $measurementService = $this->createMock(MeasurementServiceInterface::class);
        $expressionLanguage = $this->createMock(ExpressionLanguage::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf(
            'Invalid measurement conversion formula. Expected the key "%s" to exist. Check '
            . '"ibexa.measurement.value.converter.formulas" container parameter.',
            $expectedMissingField,
        ));
        /** @phpstan-ignore-next-line intentionally passing wrong formula */
        new class($measurementService, $expressionLanguage, $formulas) extends AbstractFormulaUnitConverter {
            protected function getSupportedClass(): string
            {
                throw new \LogicException();
            }

            public function convert(ValueInterface $sourceValue, UnitInterface $targetUnit): ValueInterface
            {
                throw new \LogicException();
            }
        };
    }

    /**
     * @return iterable<string, array{non-empty-array<mixed>, non-empty-string}>
     */
    public function provideForInstantiationFailure(): iterable
    {
        yield 'Formula missing' => [
            [
                ['source_unit' => 'foo', 'target_unit' => 'bar'],
            ],
            'formula',
        ];

        yield 'Target unit missing' => [
            [
                ['source_unit' => 'foo', 'formula' => 'value'],
            ],
            'target_unit',
        ];

        yield 'Source unit missing' => [
            [
                ['target_unit' => 'bar', 'formula' => 'value'],
            ],
            'source_unit',
        ];

        yield 'All missing' => [
            [
                [],
            ],
            'source_unit',
        ];
    }
}
