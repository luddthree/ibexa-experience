<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\EmailAddressComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class EmailAddressTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezemail';
    }

    public function dataToCompare(): array
    {
        return [
            [
                'test@ibexa.co',
                'changed@ibexa.co',
                new EmailAddressComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'test@ibexa.co'),
                        new TokenStringDiff(DiffStatus::ADDED, 'changed@ibexa.co'),
                    ])
                ),
            ],
            [
                'test@ibexa.co',
                'test@ibexa.co',
                new NoComparisonResult(),
            ],
            [
                null,
                null,
                new NoComparisonResult(),
            ],
            [
                '',
                null,
                new NoComparisonResult(),
            ],
        ];
    }
}

class_alias(EmailAddressTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\EmailAddressTest');
