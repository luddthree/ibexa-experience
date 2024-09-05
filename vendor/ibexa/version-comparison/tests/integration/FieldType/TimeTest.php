<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\TimeComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;

class TimeTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'eztime';
    }

    public function dataToCompare(): array
    {
        return [
            [
                100,
                200,
                new TimeComparisonResult(
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, 100),
                        new IntegerDiff(DiffStatus::ADDED, 200),
                    )
                ),
            ],
            [
                200,
                200,
                new NoComparisonResult(),
            ],
            [
                null,
                200,
                new TimeComparisonResult(
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, null),
                        new IntegerDiff(DiffStatus::ADDED, 200),
                    )
                ),
            ],
            [
                300,
                null,
                new TimeComparisonResult(
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, 300),
                        new IntegerDiff(DiffStatus::ADDED, null),
                    )
                ),
            ],
            [
                null,
                null,
                new NoComparisonResult(),
            ],
        ];
    }
}

class_alias(TimeTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\TimeTest');
