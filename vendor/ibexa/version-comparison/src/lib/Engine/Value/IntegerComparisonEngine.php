<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;

final class IntegerComparisonEngine
{
    public function compareValues(
        IntegerComparisonValue $integerA,
        IntegerComparisonValue $integerB
    ): IntegerComparisonResult {
        $statusA = DiffStatus::REMOVED;
        $statusB = DiffStatus::ADDED;

        if ($integerA->value === $integerB->value) {
            $statusA = $statusB = DiffStatus::UNCHANGED;
        }

        return new IntegerComparisonResult(
            new IntegerDiff(
                $statusA,
                $integerA->value
            ),
            new IntegerDiff(
                $statusB,
                $integerB->value
            )
        );
    }
}

class_alias(IntegerComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\IntegerComparisonEngine');
