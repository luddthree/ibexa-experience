<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Result;

use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\TextLineComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use PHPUnit\Framework\TestCase;

class TextLineComparisonResultTest extends TestCase
{
    /**
     * @dataProvider isChangedDataProvider
     *
     * @param \Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff[] $stringDiffs
     */
    public function testIsChanged(array $stringDiffs, bool $expectedResult)
    {
        $result = new TextLineComparisonResult(new StringComparisonResult($stringDiffs));

        $this->assertEquals(
            $expectedResult,
            $result->isChanged()
        );
    }

    public function isChangedDataProvider()
    {
        return [
            [
                [
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged unchanged'
                    ),
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        'removed removed'
                    ),
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        'added added'
                    ),
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged_again merge'
                    ),
                ],
                true,
            ],
            [
                [
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged unchanged'
                    ),
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged_again merge'
                    ),
                ],
                false,
            ],
        ];
    }
}

class_alias(TextLineComparisonResultTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Result\TextLineComparisonResultTest');
