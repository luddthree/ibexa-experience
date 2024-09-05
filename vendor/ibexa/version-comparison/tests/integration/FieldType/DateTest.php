<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use DateTime;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\DateComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff;

class DateTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezdate';
    }

    public function dataToCompare(): array
    {
        return [
            [
                '12-01-2018',
                '12-01-2019',
                new DateComparisonResult(
                    new DateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            new DateTime('12-01-2018')
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            new DateTime('12-01-2019')
                        ),
                    )
                ),
            ],
            [
                '12-01-2018',
                '12-01-2018',
                new NoComparisonResult(),
            ],
            [
                null,
                '12-01-2019',
                new DateComparisonResult(
                    new DateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            null
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            new DateTime('12-01-2019')
                        ),
                    )
                ),
            ],
            [
                '12-01-2019',
                null,
                new DateComparisonResult(
                    new DateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            new DateTime('12-01-2019')
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            null
                        ),
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

class_alias(DateTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\DateTest');
