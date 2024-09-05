<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\VersionComparison\Engine;

use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;
use Ibexa\VersionComparison\Engine\FieldType\MatrixComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\FieldType\Matrix\Row;
use Ibexa\VersionComparison\FieldType\Matrix\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Matrix\MatrixComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;
use PHPUnit\Framework\TestCase;

final class MatrixComparisonEngineTest extends TestCase
{
    /** @var \Ibexa\VersionComparison\Engine\FieldType\DateComparisonEngine */
    private $engine;

    protected function setUp(): void
    {
        $this->engine = new MatrixComparisonEngine(
            new StringComparisonEngine()
        );
    }

    public function fieldsAndResultProvider(): array
    {
        return [
            'value_did_not_change' => [
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1 Cell 1',
                            ]),
                            'column_2' => new StringComparisonValue([
                                'value' => 'Row 1 Cell 2',
                            ]),
                        ]]),
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 2 Cell 1',
                            ]),
                            'column_2' => new StringComparisonValue([
                                'value' => 'Row 2 Cell 2',
                            ]),
                        ]]),
                    ],
                ]),
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1 Cell 1',
                            ]),
                            'column_2' => new StringComparisonValue([
                                'value' => 'Row 1 Cell 2',
                            ]),
                        ]]),
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 2 Cell 1',
                            ]),
                            'column_2' => new StringComparisonValue([
                                'value' => 'Row 2 Cell 2',
                            ]),
                        ]]),
                    ],
                ]),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'Row 1 Cell 1'
                                ),
                            ])
                            ),
                            'column_2' => new CellComparisonResult(new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'Row 1 Cell 2'
                                ),
                            ])),
                        ]),
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'Row 2 Cell 1'
                                ),
                            ])),
                            'column_2' => new CellComparisonResult(new StringComparisonResult([
                                new TokenStringDiff(
                                    DiffStatus::UNCHANGED,
                                    'Row 2 Cell 2'
                                ),
                            ])),
                        ]),
                    ]
                ),
            ],
            'changed_values' => [
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Old cell',
                            ]),
                        ]]),
                    ],
                ]),
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'New cell',
                            ]),
                        ]]),
                    ],
                ]),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::REMOVED,
                                        'Old '
                                    ),
                                    new TokenStringDiff(
                                        DiffStatus::ADDED,
                                        'New '
                                    ),
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'cell'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
            'extra_row' => [
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                        ]]),
                    ],
                ]),
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                        ]]),
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 2',
                            ]),
                        ]]),
                    ],
                ]),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1'
                                    ),
                                ])
                            ),
                        ]),
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::ADDED,
                                        'Row 2'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
            'removed_row' => [
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                        ]]),
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 2',
                            ]),
                        ]]),
                    ],
                ]),
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                        ]]),
                    ],
                ]),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1'
                                    ),
                                ])
                            ),
                        ]),
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::REMOVED,
                                        'Row 2'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
            'extra_column' => [
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1 column 1',
                            ]),
                        ]]),
                    ],
                ]),
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1 column 1',
                            ]),
                            'column_2' => new StringComparisonValue([
                                'value' => 'Row 1 column 2',
                            ]),
                        ]]),
                    ],
                ]),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1 column 1'
                                    ),
                                ])
                            ),
                            'column_2' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::ADDED,
                                        'Row 1 column 2'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
            'removed_column' => [
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                            'column_2' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                        ]]),
                    ],
                ]),
                new Value([
                    'rows' => [
                        new Row(['cells' => [
                            'column_1' => new StringComparisonValue([
                                'value' => 'Row 1',
                            ]),
                        ]]),
                    ],
                ]),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'column_1' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1'
                                    ),
                                ])
                            ),
                            'column_2' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::REMOVED,
                                        'Row 1'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
        ];
    }

    /**
     * @dataProvider fieldsAndResultProvider
     */
    public function testCompareFieldsData(
        Value $matrixA,
        Value $matrixB,
        MatrixComparisonResult $expected
    ): void {
        $this->assertEquals(
            $expected,
            $this->engine->compareFieldsTypeValues(
                $matrixA,
                $matrixB,
            )
        );
    }
}

class_alias(MatrixComparisonEngineTest::class, 'EzSystems\EzPlatformVersionComparison\Tests\Engine\MatrixComparisonEngineTest');
