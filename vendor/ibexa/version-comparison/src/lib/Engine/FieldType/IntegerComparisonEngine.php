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
use Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine as ValueIntegerComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\IntegerComparisonResult;

final class IntegerComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $integerComparisonEngine;

    public function __construct(ValueIntegerComparisonEngine $integerComparisonEngine)
    {
        $this->integerComparisonEngine = $integerComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Integer\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Integer\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $intResult = $this->integerComparisonEngine->compareValues(
            $comparisonDataA->integer,
            $comparisonDataB->integer
        );

        return new IntegerComparisonResult($intResult);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Integer\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Integer\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->integer->value !== $comparisonDataB->integer->value;
    }
}

class_alias(IntegerComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\IntegerComparisonEngine');
