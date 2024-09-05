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
use Ibexa\VersionComparison\Engine\Value\BooleanComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\CheckboxComparisonResult;

final class CheckboxComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\BooleanComparisonEngine */
    private $booleanComparisonEngine;

    public function __construct(BooleanComparisonEngine $booleanComparisonEngine)
    {
        $this->booleanComparisonEngine = $booleanComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Checkbox\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Checkbox\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $comparisonResult = $this->booleanComparisonEngine->compareValues(
            $comparisonDataA->checkbox,
            $comparisonDataB->checkbox
        );

        return new CheckboxComparisonResult($comparisonResult);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Checkbox\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Checkbox\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->checkbox->value !== $comparisonDataB->checkbox->value;
    }
}

class_alias(CheckboxComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\CheckboxComparisonEngine');
