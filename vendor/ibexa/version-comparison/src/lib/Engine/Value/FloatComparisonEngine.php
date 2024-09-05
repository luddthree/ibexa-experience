<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\FloatComparisonValue;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\FloatDiff;
use Ibexa\VersionComparison\Result\Value\FloatComparisonResult;

final class FloatComparisonEngine
{
    public function compareValues(
        FloatComparisonValue $floatA,
        FloatComparisonValue $floatB
    ): FloatComparisonResult {
        $statusA = DiffStatus::REMOVED;
        $statusB = DiffStatus::ADDED;

        if ($floatA->value === $floatB->value) {
            $statusA = $statusB = DiffStatus::UNCHANGED;
        }

        return new FloatComparisonResult(
            new FloatDiff(
                $statusA,
                $floatA->value
            ),
            new FloatDiff(
                $statusB,
                $floatB->value
            )
        );
    }
}

class_alias(FloatComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\FloatComparisonEngine');
