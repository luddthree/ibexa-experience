<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class BinaryFileTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezbinaryfile';
    }

    public function dataToCompare(): array
    {
        return [
            [
                [
                    'id' => null,
                    'fileName' => 'Icy-Night-Flower-Binary.jpg',
                    'fileSize' => 2137,
                    'mimeType' => 'image/jpeg',
                ],
                [
                    'id' => null,
                    'fileName' => 'Icy-Night-Flower-Binary.jpg',
                    'fileSize' => 2137,
                    'mimeType' => 'image/jpeg',
                ],
                new NoComparisonResult(),
            ],
            [
                [
                    'id' => null,
                    'fileName' => '1234aaaa1234-Standard logo.jpg',
                    'inputUri' => ($path = __DIR__ . '/../_fixtures/images/1234aaaa1234-Standard-logo.jpg'),
                    'fileSize' => filesize($path),
                    'mimeType' => 'image/jpeg',
                ],
                [
                    'id' => null,
                    'fileName' => '1234aaaa1234-Dark-logo.png',
                    'inputUri' => ($path = __DIR__ . '/../_fixtures/images/1234aaaa1234-Dark-logo.png'),
                    'fileSize' => filesize($path),
                    'mimeType' => 'image/png',
                ],
                new BinaryFileComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, '1234aaaa1234-Standard logo.jpg'),
                        new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Dark-logo.png'),
                    ]),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, 15375),
                        new IntegerDiff(DiffStatus::ADDED, 3615),
                    ),
                ),
            ],
            [
                null,
                [
                    'id' => null,
                    'fileName' => '1234aaaa1234-Dark-logo.png',
                    'inputUri' => ($path = __DIR__ . '/../_fixtures/images/1234aaaa1234-Dark-logo.png'),
                    'fileSize' => filesize($path),
                    'mimeType' => 'image/png',
                ],
                new BinaryFileComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::ADDED, '1234aaaa1234-Dark-logo.png'),
                    ]),
                    new IntegerComparisonResult(
                        new IntegerDiff(DiffStatus::REMOVED, null),
                        new IntegerDiff(DiffStatus::ADDED, 3615),
                    ),
                ),
            ],
        ];
    }
}

class_alias(BinaryFileTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\BinaryFileTest');
