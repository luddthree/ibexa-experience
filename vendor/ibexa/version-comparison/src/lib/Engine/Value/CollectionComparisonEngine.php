<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\Value;

use Ibexa\VersionComparison\ComparisonValue\Collection;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

final class CollectionComparisonEngine
{
    public function compareValues(
        Collection $collectionA,
        Collection $collectionB
    ): CollectionComparisonResult {
        if ($collectionA->compareCallable) {
            return $this->compareByCallable(
                $collectionA,
                $collectionB,
                $collectionA->compareCallable
            );
        }

        return $this->compareByValues(
            $collectionA,
            $collectionB
        );
    }

    private function compareByCallable(
        Collection $collectionA,
        Collection $collectionB,
        callable $callable
    ): CollectionComparisonResult {
        $collectionArrayA = $collectionA->collection ?? [];
        $collectionArrayB = $collectionB->collection ?? [];

        $removedList = array_udiff(
            $collectionArrayA,
            $collectionArrayB,
            $callable
        );

        $unchangedList = array_uintersect(
            $collectionArrayA,
            $collectionArrayB,
            $callable
        );

        $addedList = array_udiff(
            $collectionArrayB,
            $collectionArrayA,
            $callable
        );

        return new CollectionComparisonResult(
            new CollectionDiff(DiffStatus::REMOVED, array_values($removedList)),
            new CollectionDiff(DiffStatus::UNCHANGED, array_values($unchangedList)),
            new CollectionDiff(DiffStatus::ADDED, array_values($addedList))
        );
    }

    private function compareByValues(
        Collection $collectionA,
        Collection $collectionB
    ): CollectionComparisonResult {
        $collectionArrayA = $collectionA->collection ?? [];
        $collectionArrayB = $collectionB->collection ?? [];

        $removedList = array_diff(
            $collectionArrayA,
            $collectionArrayB,
        );

        $unchangedList = array_intersect(
            $collectionArrayA,
            $collectionArrayB,
        );

        $addedList = array_diff(
            $collectionArrayB,
            $collectionArrayA
        );

        return new CollectionComparisonResult(
            new CollectionDiff(DiffStatus::REMOVED, array_values($removedList)),
            new CollectionDiff(DiffStatus::UNCHANGED, array_values($unchangedList)),
            new CollectionDiff(DiffStatus::ADDED, array_values($addedList))
        );
    }
}

class_alias(CollectionComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\Value\CollectionComparisonEngine');
