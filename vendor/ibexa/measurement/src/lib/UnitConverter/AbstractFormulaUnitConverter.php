<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Converter\UnitConverterInterface;
use Ibexa\Contracts\Measurement\MeasurementServiceInterface;
use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;
use Ibexa\Measurement\UnitConverter\Exception\InvalidFormulaException;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;
use Webmozart\Assert\Assert;

/**
 * @template T of \Ibexa\Contracts\Measurement\Value\ValueInterface
 */
abstract class AbstractFormulaUnitConverter implements UnitConverterInterface
{
    private const FORMULA_INVALID_MESSAGE = 'Invalid measurement conversion formula. Expected the key %s to exist. '
        . 'Check "ibexa.measurement.value.converter.formulas" container parameter.';

    protected MeasurementServiceInterface $measurementService;

    private ExpressionLanguage $expressionLanguage;

    /** @var string[] */
    private array $formulas;

    /**
     * @param array<array{source_unit: string, target_unit: string, formula: string}> $formulas
     */
    public function __construct(
        MeasurementServiceInterface $measurementService,
        ExpressionLanguage $expressionLanguage,
        array $formulas
    ) {
        $this->measurementService = $measurementService;
        $this->expressionLanguage = $expressionLanguage;

        Assert::allKeyExists($formulas, 'source_unit', self::FORMULA_INVALID_MESSAGE);
        Assert::allKeyExists($formulas, 'target_unit', self::FORMULA_INVALID_MESSAGE);
        Assert::allKeyExists($formulas, 'formula', self::FORMULA_INVALID_MESSAGE);
        foreach ($formulas as $formula) {
            $key = $this->buildFormulaKey($formula['source_unit'], $formula['target_unit']);
            $this->formulas[$key] = $formula['formula'];
        }
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getSupportedClass(): string;

    final public function supports(ValueInterface $sourceValue, UnitInterface $toUnit): bool
    {
        if (!is_a($sourceValue, $this->getSupportedClass())) {
            return false;
        }

        return $this->formulaExists($sourceValue->getUnit(), $toUnit);
    }

    final protected function formulaExists(UnitInterface $source, UnitInterface $target): bool
    {
        $key = $this->buildFormulaKey($source->getIdentifier(), $target->getIdentifier());

        return isset($this->formulas[$key]);
    }

    final protected function getFormula(UnitInterface $source, UnitInterface $target): string
    {
        $key = $this->buildFormulaKey($source->getIdentifier(), $target->getIdentifier());

        return $this->formulas[$key];
    }

    final protected function doConvert(string $formula, float $value): float
    {
        try {
            $result = $this->expressionLanguage->evaluate(
                $formula,
                [
                    'value' => $value,
                ]
            );
        } catch (SyntaxError $e) {
            throw new InvalidFormulaException($formula, $e->getMessage(), $e);
        }

        if (!is_numeric($result)) {
            throw new InvalidFormulaException($formula, 'Non numeric value received as formula result.');
        }

        return (float)$result;
    }

    private function buildFormulaKey(string $source, string $target): string
    {
        return sprintf('%s-%s', $source, $target);
    }
}
