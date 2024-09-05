<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Engine;

use Ibexa\VersionComparison\ComparisonValue\DateTimeComparisonValue;
use Ibexa\VersionComparison\Engine\FieldType\DateComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\DateTimeComparisonEngine;
use Ibexa\VersionComparison\FieldType\Date\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\DateComparisonResult;
use Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff;
use PHPUnit\Framework\TestCase;

class DateComparisonEngineTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\Engine\FieldType\DateComparisonEngine */
    private $engine;

    protected function setUp(): void
    {
        $this->engine = new DateComparisonEngine(
            new DateTimeComparisonEngine()
        );
    }

    public function fieldsAndResultProvider(): array
    {
        return [
            'value_did_not_change' => [
                new DateTimeComparisonValue(['value' => new \DateTime('2018-01-01')]),
                new DateTimeComparisonValue(['value' => new \DateTime('2018-01-01')]),
                new DateComparisonResult(
                    new DateTimeComparisonResult(
                        new DateTimeDiff(
                            DiffStatus::UNCHANGED,
                            new \DateTime('2018-01-01')
                        ),
                        new DateTimeDiff(
                            DiffStatus::UNCHANGED,
                            new \DateTime('2018-01-01')
                        ),
                    )
                ),
            ],
        ];
    }

    /**
     * @dataProvider fieldsAndResultProvider
     */
    public function testCompareFieldsData(
        DateTimeComparisonValue $fieldA,
        DateTimeComparisonValue $fieldB,
        DateComparisonResult $expected
    ): void {
        $this->assertEquals(
            $expected,
            $this->engine->compareFieldsTypeValues(
                new Value(['date' => $fieldA]),
                new Value(['date' => $fieldB]),
            )
        );
    }
}

class_alias(DateComparisonEngineTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Engine\DateComparisonEngineTest');
