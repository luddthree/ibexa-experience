<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\DateTimeComparisonValue;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff;

final class DateTimeComparisonEngine
{
    public function compareValues(
        DateTimeComparisonValue $dateTimeA,
        DateTimeComparisonValue $dateTimeB
    ): DateTimeComparisonResult {
        $statusA = DiffStatus::REMOVED;
        $statusB = DiffStatus::ADDED;

        if ($dateTimeA->value == $dateTimeB->value) {
            $statusA = $statusB = DiffStatus::UNCHANGED;
        }

        return new DateTimeComparisonResult(
            new DateTimeDiff(
                $statusA,
                $dateTimeA->value
            ),
            new DateTimeDiff(
                $statusB,
                $dateTimeB->value
            )
        );
    }
}

class_alias(DateTimeComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\DateTimeComparisonEngine');
