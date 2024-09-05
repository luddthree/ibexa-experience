<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult as ValueIntegerComparisonResult;

class IntegerTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezinteger';
    }

    public function dataToCompare(): array
    {
        return [
            [
                10,
                20,
                new IntegerComparisonResult(
                    new ValueIntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, 10),
                        new IntegerDiff(DiffStatus::ADDED, 20)
                    )
                ),
            ],
            [
                20,
                20,
                new NoComparisonResult(),
            ],
            [
                null,
                20,
                new IntegerComparisonResult(
                    new ValueIntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, null),
                        new IntegerDiff(DiffStatus::ADDED, 20)
                    )
                ),
            ],
            [
                null,
                20,
                new IntegerComparisonResult(
                    new ValueIntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, null),
                        new IntegerDiff(DiffStatus::ADDED, 20)
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

class_alias(IntegerTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\IntegerTest');
