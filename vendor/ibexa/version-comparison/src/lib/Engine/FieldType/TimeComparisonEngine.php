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
use Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\TimeComparisonResult;

final class TimeComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $integerComparisonEngine;

    public function __construct(IntegerComparisonEngine $integerComparisonEngine)
    {
        $this->integerComparisonEngine = $integerComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Time\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Time\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $intResult = $this->integerComparisonEngine->compareValues(
            $comparisonDataA->time,
            $comparisonDataB->time
        );

        return new TimeComparisonResult($intResult);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Time\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Time\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->time->value !== $comparisonDataB->time->value;
    }
}

class_alias(TimeComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\TimeComparisonEngine');
