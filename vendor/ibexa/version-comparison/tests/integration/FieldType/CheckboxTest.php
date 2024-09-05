<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\CheckboxComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\BooleanComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\BooleanDiff;

class CheckboxTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezboolean';
    }

    public function dataToCompare(): array
    {
        return [
            [
                true,
                false,
                new CheckboxComparisonResult(
                    new BooleanComparisonResult(
                        new BooleanDiff(DiffStatus::REMOVED, true),
                        new BooleanDiff(DiffStatus::ADDED, false),
                    )
                ),
            ],
            //Empty value is treated the same as false in Kernel.
            [
                null,
                false,
                new NoComparisonResult(),
            ],
            [
                false,
                false,
                new NoComparisonResult(),
            ],
            [
                null,
                null,
                new NoComparisonResult(),
            ],
        ];
    }
}

class_alias(CheckboxTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\CheckboxTest');
