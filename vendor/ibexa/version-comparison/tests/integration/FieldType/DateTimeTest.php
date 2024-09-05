<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use DateTime;
use DateTimeZone;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\DateTimeComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult as ValueDateTimeComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;

class DateTimeTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezdatetime';
    }

    public function dataToCompare(): array
    {
        return [
            [
                '12-01-2018 13:00 UTC',
                '12-01-2019 17:15 UTC',
                new DateTimeComparisonResult(
                    new ValueDateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            new DateTime('12-01-2018', new DateTimeZone('UTC'))
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            new DateTime('12-01-2019', new DateTimeZone('UTC'))
                        ),
                    ),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, 13 * 3600),
                        new IntegerDiff(DiffStatus::ADDED, 17 * 3600 + 15 * 60),
                    )
                ),
            ],
            [
                '10-01-2018 13:00 UTC',
                '12-01-2019 13:00 UTC',
                new DateTimeComparisonResult(
                    new ValueDateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            new DateTime('10-01-2018', new DateTimeZone('UTC'))
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            new DateTime('12-01-2019', new DateTimeZone('UTC'))
                        ),
                    ),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::UNCHANGED, 13 * 3600),
                        new IntegerDiff(DiffStatus::UNCHANGED, 13 * 3600),
                    )
                ),
            ],
            [
                '12-01-2018 14:00 Europe/Warsaw',
                '12-01-2018 8:00 America/New_York',
                new NoComparisonResult(),
            ],
            [
                '12-01-2018 13:00',
                '12-01-2018 13:00',
                new NoComparisonResult(),
            ],
            [
                null,
                '12-01-2019 12:45 UTC',
                new DateTimeComparisonResult(
                    new ValueDateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            null
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            new DateTime('12-01-2019', new DateTimeZone('UTC'))
                        ),
                    ),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, null),
                        new IntegerDiff(DiffStatus::ADDED, 12 * 3600 + 45 * 60),
                    )
                ),
            ],
            [
                '12-01-2019 00:00 UTC',
                null,
                new DateTimeComparisonResult(
                    new ValueDateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::REMOVED,
                            new DateTime('12-01-2019', new DateTimeZone('UTC'))
                        ),
                        new DateTimeDiff(
                            DiffStatus::ADDED,
                            null
                        ),
                    ),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, 0),
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

class_alias(DateTimeTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\DateTimeTest');
