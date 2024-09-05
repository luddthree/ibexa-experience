<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Measurement\UnitConverter;

use Ibexa\Contracts\Measurement\Value\Definition\UnitInterface;
use Ibexa\Contracts\Measurement\Value\RangeValueInterface;
use Ibexa\Contracts\Measurement\Value\ValueInterface;

/**
 * @extends \Ibexa\Measurement\UnitConverter\AbstractFormulaUnitConverter<
 *     \Ibexa\Contracts\Measurement\Value\RangeValueInterface,
 * >
 */
final class RangeValueFormulaUnitConverter extends AbstractFormulaUnitConverter
{
    protected function getSupportedClass(): string
    {
        return RangeValueInterface::class;
    }

    /**
     * @param \Ibexa\Contracts\Measurement\Value\RangeValueInterface $sourceValue
     */
    public function convert(ValueInterface $sourceValue, UnitInterface $targetUnit): RangeValueInterface
    {
        $formula = $this->getFormula($sourceValue->getUnit(), $targetUnit);

        return $this->measurementService->buildRangeValue(
            $sourceValue->getMeasurement()->getName(),
            $this->doConvert($formula, $sourceValue->getMinValue()),
            $this->doConvert($formula, $sourceValue->getMaxValue()),
            $targetUnit->getIdentifier()
        );
    }
}
