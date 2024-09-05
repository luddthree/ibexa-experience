<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\BooleanComparisonValue;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\BooleanComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BooleanDiff;

final class BooleanComparisonEngine
{
    public function compareValues(
        BooleanComparisonValue $boolA,
        BooleanComparisonValue $boolB
    ): BooleanComparisonResult {
        $statusA = DiffStatus::REMOVED;
        $statusB = DiffStatus::ADDED;

        if ($boolA->value === $boolB->value) {
            $statusA = $statusB = DiffStatus::UNCHANGED;
        }

        return new BooleanComparisonResult(
            new BooleanDiff(
                $statusA,
                $boolA->value
            ),
            new BooleanDiff(
                $statusB,
                $boolB->value
            )
        );
    }
}

class_alias(BooleanComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\BooleanComparisonEngine');
