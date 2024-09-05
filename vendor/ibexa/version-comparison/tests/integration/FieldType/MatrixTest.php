<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\VersionComparison\FieldType;

use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinitionCreateStruct;
use Ibexa\FieldTypeMatrix\FieldType\Value;
use Ibexa\FieldTypeMatrix\FieldType\Value\Row;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Matrix\MatrixComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class MatrixTest extends AbstractFieldTypeComparisonTest
{
    protected function getFieldType(): string
    {
        return 'ezmatrix';
    }

    protected function buildFieldDefinitionCreateStruct(string $fieldType): FieldDefinitionCreateStruct
    {
        $titleFieldCreate = $this->contentTypeService->newFieldDefinitionCreateStruct($fieldType, $fieldType);
        $titleFieldCreate->names = [
            'eng-GB' => $fieldType,
        ];
        $titleFieldCreate->fieldSettings = [
            'columns' => [
                1 => [
                    'identifier' => 'first',
                ],
                2 => [
                    'identifier' => 'second',
                ],
            ],
        ];

        return $titleFieldCreate;
    }

    public function dataToCompare(): array
    {
        return [
            'no_change' => [
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                        new Row([
                            'first' => 'Row 2 column 1',
                            'second' => 'Row 2 column 2',
                        ]),
                    ]
                ),
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                        new Row([
                            'first' => 'Row 2 column 1',
                            'second' => 'Row 2 column 2',
                        ]),
                    ]
                ),
                new NoComparisonResult(),
            ],
            'value_change' => [
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                    ]
                ),
                new Value(
                    [
                        new Row([
                            'first' => 'Edited Value',
                            'second' => 'Row 1 column 2',
                        ]),
                    ]
                ),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'first' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::REMOVED,
                                        'Row 1 column 1'
                                    ),
                                    new TokenStringDiff(
                                        DiffStatus::ADDED,
                                        'Edited Value'
                                    ),
                                ])
                            ),
                            'second' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1 column 2'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
            'extra_row' => [
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                    ]
                ),
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                        new Row([
                            'first' => 'Row 2 column 1',
                            'second' => 'Row 2 column 2',
                        ]),
                    ]
                ),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'first' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1 column 1'
                                    ),
                                ])
                            ),
                            'second' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1 column 2'
                                    ),
                                ])
                            ),
                        ]),
                        new RowComparisonResult([
                            'first' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::ADDED,
                                        'Row 2 column 1'
                                    ),
                                ])
                            ),
                            'second' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::ADDED,
                                        'Row 2 column 2'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
            'removed_row' => [
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                        new Row([
                            'first' => 'Row 2 column 1',
                            'second' => 'Row 2 column 2',
                        ]),
                    ]
                ),
                new Value(
                    [
                        new Row([
                            'first' => 'Row 1 column 1',
                            'second' => 'Row 1 column 2',
                        ]),
                    ]
                ),
                new MatrixComparisonResult(
                    [
                        new RowComparisonResult([
                            'first' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1 column 1'
                                    ),
                                ])
                            ),
                            'second' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::UNCHANGED,
                                        'Row 1 column 2'
                                    ),
                                ])
                            ),
                        ]),
                        new RowComparisonResult([
                            'first' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::REMOVED,
                                        'Row 2 column 1'
                                    ),
                                ])
                            ),
                            'second' => new CellComparisonResult(
                                new StringComparisonResult([
                                    new TokenStringDiff(
                                        DiffStatus::REMOVED,
                                        'Row 2 column 2'
                                    ),
                                ])
                            ),
                        ]),
                    ]
                ),
            ],
        ];
    }
}

class_alias(MatrixTest::class, 'EzSystems\EzPlatformVersionComparison\Integration\Tests\FieldType\MatrixTest');
