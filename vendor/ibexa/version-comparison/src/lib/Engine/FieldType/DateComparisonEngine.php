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
use Ibexa\VersionComparison\Engine\Value\DateTimeComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\DateComparisonResult;

final class DateComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\DateTimeComparisonEngine */
    private $dateTimeComparisonEngine;

    public function __construct(DateTimeComparisonEngine $dateTimeComparisonEngine)
    {
        $this->dateTimeComparisonEngine = $dateTimeComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Date\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Date\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $dateTimeResult = $this->dateTimeComparisonEngine->compareValues(
            $comparisonDataA->date,
            $comparisonDataB->date
        );

        return new DateComparisonResult($dateTimeResult);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Date\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Date\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->date->value != $comparisonDataB->date->value;
    }
}

class_alias(DateComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\DateComparisonEngine');
