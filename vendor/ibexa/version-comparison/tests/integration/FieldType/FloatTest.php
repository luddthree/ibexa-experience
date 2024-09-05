<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\FloatComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\FloatDiff;
use Ibexa\VersionComparison\Result\Value\FloatComparisonResult as ValueFloatComparisonResult;

class FloatTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezfloat';
    }

    public function dataToCompare(): array
    {
        return [
            [
                10.5,
                20.4,
                new FloatComparisonResult(
                    new ValueFloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, 10.5),
                        new FloatDiff(DiffStatus::ADDED, 20.4),
                    )
                ),
            ],
            [
                20.3,
                20.3,
                new NoComparisonResult(),
            ],
            [
                null,
                20.1,
                new FloatComparisonResult(
                    new ValueFloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, null),
                        new FloatDiff(DiffStatus::ADDED, 20.1),
                    )
                ),
            ],
            [
                null,
                20.1,
                new FloatComparisonResult(
                    new ValueFloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, null),
                        new FloatDiff(DiffStatus::ADDED, 20.1),
                    )
                ),
            ],
            [
                10,
                20.5,
                new FloatComparisonResult(
                    new ValueFloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, 10),
                        new FloatDiff(DiffStatus::ADDED, 20.5)
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

class_alias(FloatTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\FloatTest');
