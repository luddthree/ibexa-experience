<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Core\FieldType\Url\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\UrlComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class UrlTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezurl';
    }

    public function dataToCompare(): array
    {
        return [
            [
                new Value('http://ibexa.co', 'Ibexa'),
                new Value('http://ibexa.co', 'Ibexa'),
                new NoComparisonResult(),
            ],
            [
                new Value('http://ibexa.co', 'Ibexa official website'),
                new Value('http://ibexa.no', 'Ibexa corporate website'),
                new UrlComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'http://ibexa.co'),
                        new TokenStringDiff(DiffStatus::ADDED, 'http://ibexa.no'),
                    ]),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::UNCHANGED, 'Ibexa '),
                        new TokenStringDiff(DiffStatus::REMOVED, 'official '),
                        new TokenStringDiff(DiffStatus::ADDED, 'corporate '),
                        new TokenStringDiff(DiffStatus::UNCHANGED, 'website'),
                    ]),
                ),
            ],
            [
                new Value('http://ibexa.co/one/two/three/fourth', 'Ibexa official website'),
                new Value('https://ibexa.no/raz/two/three/cztery', 'Ibexa official website'),
                new UrlComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::REMOVED, 'http://ibexa.co/one/two/three/fourth'),
                        new TokenStringDiff(DiffStatus::ADDED, 'https://ibexa.no/raz/two/three/cztery'),
                    ]),
                    new StringComparisonResult([
                        new TokenStringDiff(DiffStatus::UNCHANGED, 'Ibexa official website'),
                    ]),
                ),
            ],
        ];
    }
}

class_alias(UrlTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\UrlTest');
