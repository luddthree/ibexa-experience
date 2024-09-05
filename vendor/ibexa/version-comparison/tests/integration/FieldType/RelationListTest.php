<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\RelationListComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

class RelationListTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezobjectrelationlist';
    }

    public function dataToCompare(): array
    {
        return [
            [
                [4, 42],
                [4, 42],
                new NoComparisonResult(),
            ],
            [
                [4, 42],
                [42, 52],
                new RelationListComparisonResult(
                    new CollectionComparisonResult(
                        new CollectionDiff(DiffStatus::REMOVED, [4]),
                        new CollectionDiff(DiffStatus::UNCHANGED, [42]),
                        new CollectionDiff(DiffStatus::ADDED, [52]),
                    )
                ),
            ],
            [
                null,
                [42],
                new RelationListComparisonResult(
                    new CollectionComparisonResult(
                        new CollectionDiff(DiffStatus::REMOVED, []),
                        new CollectionDiff(DiffStatus::UNCHANGED, []),
                        new CollectionDiff(DiffStatus::ADDED, [42]),
                    )
                ),
            ],
        ];
    }
}

class_alias(RelationListTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\RelationListTest');
