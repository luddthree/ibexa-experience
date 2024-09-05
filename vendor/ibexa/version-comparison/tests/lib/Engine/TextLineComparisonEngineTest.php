<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Engine;

use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;
use Ibexa\VersionComparison\Engine\FieldType\TextLineComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\FieldType\TextLine\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\TextLineComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use PHPUnit\Framework\TestCase;

class TextLineComparisonEngineTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\Engine\FieldType\TextLineComparisonEngine */
    private $engine;

    protected function setUp(): void
    {
        $this->engine = new TextLineComparisonEngine(
            new StringComparisonEngine()
        );
    }

    public function fieldsAndResultProvider(): array
    {
        return [
            'value_did_not_change' => [
                new StringComparisonValue(['value' => 'No Change Value']),
                new StringComparisonValue(['value' => 'No Change Value']),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'No Change Value'
                    ),
                ])),
            ],
            'value_was_added' => [
                new StringComparisonValue(['value' => '']),
                new StringComparisonValue(['value' => 'Added Value']),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        'Added Value'
                    ),
                ])),
            ],
            'value_was_removed' => [
                new StringComparisonValue(['value' => 'Removed Value']),
                new StringComparisonValue(['value' => '']),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        'Removed Value'
                    ),
                ])),
            ],
            'empty_value_not_changed' => [
                new StringComparisonValue(['value' => '']),
                new StringComparisonValue(['value' => '']),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        ''
                    ),
                ])),
            ],
            'value_was_changed' => [
                new StringComparisonValue(['value' => 'unchanged removed']),
                new StringComparisonValue(['value' => 'unchanged added']),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged '
                    ),
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        'removed'
                    ),
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        'added'
                    ),
                ])),
            ],
            'values_combined' => [
                new StringComparisonValue(['value' => 'unchanged unchanged removed removed unchanged_again merge']),
                new StringComparisonValue(['value' => 'unchanged unchanged added added unchanged_again merge']),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged unchanged '
                    ),
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        'removed removed '
                    ),
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        'added added '
                    ),
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'unchanged_again merge'
                    ),
                ])),
            ],
            'no_split' => [
                new StringComparisonValue(['value' => 'no split', 'doNotSplit' => true]),
                new StringComparisonValue(['value' => 'whole change', 'doNotSplit' => true]),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        'no split'
                    ),
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        'whole change'
                    ),
                ])),
            ],
            'split_url' => [
                new StringComparisonValue(['value' => 'http://ibexa.co/stays/test', 'splitBy' => StringComparisonValue::SPLIT_BY_DOT_AND_SLASH]),
                new StringComparisonValue(['value' => 'http://ibexa.com/stays/change', 'splitBy' => StringComparisonValue::SPLIT_BY_DOT_AND_SLASH]),
                new TextLineComparisonResult(new StringComparisonResult([
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        'http://ibexa'
                    ),
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        '.co'
                    ),
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        '.com'
                    ),
                    new TokenStringDiff(
                        DiffStatus::UNCHANGED,
                        '/stays'
                    ),
                    new TokenStringDiff(
                        DiffStatus::REMOVED,
                        '/test'
                    ),
                    new TokenStringDiff(
                        DiffStatus::ADDED,
                        '/change'
                    ),
                ])),
            ],
        ];
    }

    /**
     * @dataProvider fieldsAndResultProvider
     */
    public function testCompareFieldsData(
        StringComparisonValue $fieldA,
        StringComparisonValue $fieldB,
        TextLineComparisonResult $expected
    ): void {
        $this->assertEquals(
            $expected,
            $this->engine->compareFieldsTypeValues(
                new Value(['textLine' => $fieldA]),
                new Value(['textLine' => $fieldB]),
            )
        );
    }
}

class_alias(TextLineComparisonEngineTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Engine\TextLineComparisonEngineTest');
