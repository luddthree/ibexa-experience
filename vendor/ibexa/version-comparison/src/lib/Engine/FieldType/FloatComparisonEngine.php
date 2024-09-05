<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\FieldType;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Engine\Value\FloatComparisonEngine as ValueFloatComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\FloatComparisonResult;

final class FloatComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\FloatComparisonEngine */
    private $floatComparisonEngine;

    public function __construct(ValueFloatComparisonEngine $floatComparisonEngine)
    {
        $this->floatComparisonEngine = $floatComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Float\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Float\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $floatResult = $this->floatComparisonEngine->compareValues(
            $comparisonDataA->float,
            $comparisonDataB->float
        );

        return new FloatComparisonResult($floatResult);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Float\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Float\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->float->value !== $comparisonDataB->float->value;
    }
}

class_alias(FloatComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\FloatComparisonEngine');
