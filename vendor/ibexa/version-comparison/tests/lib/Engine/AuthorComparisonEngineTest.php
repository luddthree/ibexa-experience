<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Engine;

use Ibexa\VersionComparison\ComparisonValue\Collection;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;
use Ibexa\VersionComparison\Engine\FieldType\AuthorComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\CollectionComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\FieldType\Author\Author;
use Ibexa\VersionComparison\FieldType\Author\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\Author\AuthorComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Author\SingleAuthorComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use PHPUnit\Framework\TestCase;

class AuthorComparisonEngineTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\Engine\FieldType\AuthorComparisonEngine */
    private $engine;

    protected function setUp(): void
    {
        $this->engine = new AuthorComparisonEngine(
            new StringComparisonEngine(),
            new CollectionComparisonEngine()
        );
    }

    public function fieldsAndResultProvider(): array
    {
        $compareCallable = static function (Author $authorA, Author $authorB): int {
            return $authorA->id <=> $authorB->id;
        };

        return [
            'value_did_not_change' => [
                new Collection([
                    'collection' => [
                        new Author(
                            [
                                'id' => 1,
                                'name' => new StringComparisonValue(['value' => 'test name']),
                                'email' => new StringComparisonValue(['value' => 'test@ibexa.co']),
                            ]
                        ),
                    ],
                    'compareCallable' => $compareCallable,
                ]),
                new Collection([
                    'collection' => [
                        new Author(
                            [
                                'id' => 1,
                                'name' => new StringComparisonValue(['value' => 'test name']),
                                'email' => new StringComparisonValue(['value' => 'test@ibexa.co']),
                            ]
                        ),
                    ],
                    'compareCallable' => $compareCallable,
                ]),
                new CollectionComparisonResult(
                    new CollectionDiff(DiffStatus::REMOVED, []),
                    new CollectionDiff(DiffStatus::UNCHANGED, [
                        new SingleAuthorComparisonResult(
                            1,
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'test name'
                                ),
                            ]),
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'test@ibexa.co'
                                ),
                            ]),
                        ),
                    ]),
                    new CollectionDiff(DiffStatus::ADDED, []),
                ),
            ],
            'one_author_removed_one_changed_one_added' => [
                new Collection([
                    'collection' => [
                        new Author(
                            [
                                'id' => 1,
                                'name' => new StringComparisonValue(['value' => 'removed']),
                                'email' => new StringComparisonValue(['value' => 'rm@ibexa.co']),
                            ]
                        ),
                        new Author(
                            [
                                'id' => 2,
                                'name' => new StringComparisonValue(['value' => 'test name']),
                                'email' => new StringComparisonValue(['value' => 'test@ibexa.co']),
                            ]
                        ),
                    ],
                    'compareCallable' => $compareCallable,
                ]),
                new Collection([
                    'collection' => [
                        new Author(
                            [
                                'id' => 2,
                                'name' => new StringComparisonValue(['value' => 'changed name']),
                                'email' => new StringComparisonValue(['value' => 'test@ibexa.co']),
                            ]
                        ),
                        new Author(
                            [
                                'id' => 3,
                                'name' => new StringComparisonValue(['value' => 'new name']),
                                'email' => new StringComparisonValue(['value' => 'new@ibexa.co']),
                            ]
                        ),
                    ],
                    'compareCallable' => $compareCallable,
                ]),
                new CollectionComparisonResult(
                    new CollectionDiff(DiffStatus::REMOVED, [
                        new SingleAuthorComparisonResult(
                            1,
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::REMOVED,
                                    'removed'
                                ),
                            ]),
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::REMOVED,
                                    'rm@ibexa.co'
                                ),
                            ]),
                        ),
                    ]),
                    new CollectionDiff(DiffStatus::UNCHANGED, [
                        new SingleAuthorComparisonResult(
                            2,
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::REMOVED,
                                    'test '
                                ),
                                new TokenStringDiff(
                                    DiffStatus::ADDED,
                                    'changed '
                                ),
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'name'
                                ),
                            ]),
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'test@ibexa.co'
                                ),
                            ]),
                        ),
                    ]),
                    new CollectionDiff(DiffStatus::ADDED, [
                        new SingleAuthorComparisonResult(
                            3,
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::ADDED,
                                    'new name'
                                ),
                            ]),
                            new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::ADDED,
                                    'new@ibexa.co'
                                ),
                            ]),
                        ),
                    ]),
                ),
            ],
        ];
    }

    /**
     * @dataProvider fieldsAndResultProvider
     */
    public function testCompareFieldsData(
        Collection $authorsA,
        Collection $authorsB,
        CollectionComparisonResult $expected
    ): void {
        $this->assertEquals(
            new AuthorComparisonResult($expected),
            $this->engine->compareFieldsTypeValues(
                new Value(['authors' => $authorsA]),
                new Value(['authors' => $authorsB]),
            )
        );
    }
}

class_alias(AuthorComparisonEngineTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Engine\AuthorComparisonEngineTest');
