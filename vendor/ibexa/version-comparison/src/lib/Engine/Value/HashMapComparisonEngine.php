<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\HashMap;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

final class HashMapComparisonEngine
{
    public function compareValues(
        HashMap $collectionA,
        HashMap $collectionB
    ): CollectionComparisonResult {
        $removedList = array_diff_key(
            $collectionA->hashMap,
            $collectionB->hashMap,
        );

        $unchangedList = array_intersect_key(
            $collectionA->hashMap,
            $collectionB->hashMap,
        );

        $addedList = array_diff_key(
            $collectionB->hashMap,
            $collectionA->hashMap,
        );

        return new CollectionComparisonResult(
            new CollectionDiff(DiffStatus::REMOVED, $removedList),
            new CollectionDiff(DiffStatus::UNCHANGED, $unchangedList),
            new CollectionDiff(DiffStatus::ADDED, $addedList)
        );
    }
}

class_alias(HashMapComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\HashMapComparisonEngine');
