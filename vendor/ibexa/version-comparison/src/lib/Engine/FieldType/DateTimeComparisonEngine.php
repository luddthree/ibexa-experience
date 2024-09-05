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
use Ibexa\VersionComparison\Engine\Value\DateTimeComparisonEngine as ValueDateTimeComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\DateTimeComparisonResult;

final class DateTimeComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\DateTimeComparisonEngine */
    private $dateTimeComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $integerComparisonEngine;

    public function __construct(
        ValueDateTimeComparisonEngine $dateTimeComparisonEngine,
        IntegerComparisonEngine $integerComparisonEngine
    ) {
        $this->dateTimeComparisonEngine = $dateTimeComparisonEngine;
        $this->integerComparisonEngine = $integerComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\DateTime\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\DateTime\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $dateTimeResult = $this->dateTimeComparisonEngine->compareValues(
            $comparisonDataA->dateTime,
            $comparisonDataB->dateTime
        );
        $timeResult = $this->integerComparisonEngine->compareValues(
            $comparisonDataA->time,
            $comparisonDataB->time
        );

        return new DateTimeComparisonResult(
            $dateTimeResult,
            $timeResult
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\DateTime\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\DateTime\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return ($comparisonDataA->dateTime->value != $comparisonDataB->dateTime->value)
            || ($comparisonDataA->time->value !== $comparisonDataB->time->value);
    }
}

class_alias(DateTimeComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\DateTimeComparisonEngine');
