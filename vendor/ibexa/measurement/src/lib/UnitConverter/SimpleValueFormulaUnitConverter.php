<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\SimpleValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

/**
 * @extends \Ibexa\Measurement\UnitConverter\AbstractFormulaUnitConverter<
 *     \Ibexa\Contracts\Measurement\Value\SimpleValueInterface,
 * >
 */
final class SimpleValueFormulaUnitConverter extends AbstractFormulaUnitConverter
{
    protected function getSupportedClass(): string
    {
        return SimpleValueInterface::class;
    }

    /**
     * @param \Ibexa\Contracts\Measurement\Value\SimpleValueInterface $sourceValue
     */
    public function convert(ValueInterface $sourceValue, UnitInterface $targetUnit): SimpleValueInterface
    {
        $formula = $this->getFormula($sourceValue->getUnit(), $targetUnit);

        return $this->measurementService->buildSimpleValue(
            $sourceValue->getMeasurement()->getName(),
            $this->doConvert($formula, $sourceValue->getValue()),
            $targetUnit->getIdentifier()
        );
    }
}
