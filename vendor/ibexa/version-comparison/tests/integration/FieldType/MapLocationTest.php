<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\MapLocationComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\FloatDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\FloatComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class MapLocationTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezgmaplocation';
    }

    public function dataToCompare(): array
    {
        return [
            [
                [
                    'latitude' => 51.559997,
                    'longitude' => 6.767921,
                    'address' => 'Bielefeld',
                ],
                [
                    'latitude' => 51.559997,
                    'longitude' => 6.767921,
                    'address' => 'Bielefeld',
                ],
                new NoComparisonResult(),
            ],
            [
                [
                    'latitude' => 51.559997,
                    'longitude' => 6.767921,
                    'address' => 'Madrid, Spain',
                ],
                [
                    'latitude' => 11.997,
                    'longitude' => 4.2137,
                    'address' => 'Valencia, Spain',
                ],
                new MapLocationComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'Madrid, '),
                        new TokenStringDiff(DiffStatus::ADDED, 'Valencia, '),
                        new TokenStringDiff(DiffStatus::UNCHANGED, 'Spain'),
                    ]),
                    new FloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, 6.767921),
                        new FloatDiff(DiffStatus::ADDED, 4.2137),
                    ),
                    new FloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, 51.559997),
                        new FloatDiff(DiffStatus::ADDED, 11.997),
                    ),
                ),
            ],
            [
                [
                    'latitude' => 51.559997,
                    'longitude' => 6.767921,
                    'address' => 'Madrid, Spain',
                ],
                [
                    'latitude' => 51.559997,
                    'longitude' => 6.767921,
                    'address' => 'Valencia, Spain',
                ],
                new MapLocationComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'Madrid, '),
                        new TokenStringDiff(DiffStatus::ADDED, 'Valencia, '),
                        new TokenStringDiff(DiffStatus::UNCHANGED, 'Spain'),
                    ]),
                    new FloatComparisonResult(
                        new FloatDiff(DiffStatus::UNCHANGED, 6.767921),
                        new FloatDiff(DiffStatus::UNCHANGED, 6.767921),
                    ),
                    new FloatComparisonResult(
                        new FloatDiff(DiffStatus::UNCHANGED, 51.559997),
                        new FloatDiff(DiffStatus::UNCHANGED, 51.559997),
                    ),
                ),
            ],
            [
                [
                    'latitude' => null,
                    'longitude' => null,
                    'address' => null,
                ],
                [
                    'latitude' => 11.997,
                    'longitude' => 4.2137,
                    'address' => 'Valencia, Spain',
                ],
                new MapLocationComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::ADDED, 'Valencia, Spain'),
                    ]),
                    new FloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, null),
                        new FloatDiff(DiffStatus::ADDED, 4.2137),
                    ),
                    new FloatComparisonResult(
                        new FloatDiff(DiffStatus::REMOVED, null),
                        new FloatDiff(DiffStatus::ADDED, 11.997),
                    ),
                ),
            ],
        ];
    }
}

class_alias(MapLocationTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\MapLocationTest');
