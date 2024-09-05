<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine\FieldType;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\FieldType\Matrix\Row;
use Ibexa\VersionComparison\FieldType\Matrix\Value;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Matrix\MatrixComparisonResult;
use Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class MatrixComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Matrix\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Matrix\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $rowComparisonResults = [];

        foreach ($comparisonDataA->rows as $rowIndex => $row) {
            $rowComparisonResults[] = new RowComparisonResult(
                $this->compareCellsInRow(
                    $row->cells,
                    $rowIndex,
                    $comparisonDataB
                )
            );
        }

        // Handle now rows
        foreach ($comparisonDataB->rows as $rowIndex => $row) {
            if (\array_key_exists($rowIndex, $comparisonDataA->rows)) {
                $rowComparisonResults = $this->pushNewColumnToPreviousRows($rowComparisonResults, $rowIndex, $row);
                continue;
            }
            $cellResults = [];
            foreach ($row->cells as $columnIndex => $cell) {
                $cellResults[$columnIndex] =
                    new CellComparisonResult(
                        new StringComparisonResult([
                            new TokenStringDiff(
                                DiffStatus::ADDED,
                                $cell->value
                            ),
                        ])
                    );
            }
            $rowComparisonResults[] = new RowComparisonResult($cellResults);
        }

        return new MatrixComparisonResult($rowComparisonResults);
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Matrix\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Matrix\Value $comparisonDataB
     */
    public function shouldRunComparison(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): bool {
        return $comparisonDataA->rows != $comparisonDataB->rows;
    }

    /**
     * @param \Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult[] $rowComparisonResults
     *
     * @return \Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult[]
     */
    private function pushNewColumnToPreviousRows(
        array $rowComparisonResults,
        int $rowIndex,
        Row $row
    ): array {
        foreach ($row->cells as $columnIndex => $cell) {
            $cellResults = $rowComparisonResults[$rowIndex]->getCellsComparisonResults();
            if (\array_key_exists($columnIndex, $cellResults)) {
                continue;
            }
            $cellResults[$columnIndex] =
                new CellComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(
                            DiffStatus::ADDED,
                            $cell->value
                        ),
                    ])
                );

            $rowComparisonResults[$rowIndex] = new RowComparisonResult($cellResults);
        }

        return $rowComparisonResults;
    }

    /**
     * @param \Ibexa\VersionComparison\ComparisonValue\StringComparisonValue[] $cells
     *
     * @return \Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult[]
     */
    private function compareCellsInRow(array $cells, int $rowIndex, Value $compareTo): array
    {
        $cellResults = [];
        foreach ($cells as $columnIndex => $cell) {
            // Compare rows
            if (isset($compareTo->rows[$rowIndex]->cells[$columnIndex])) {
                $cellResults[$columnIndex] =
                    new CellComparisonResult(
                        $this->stringComparisonEngine->compareValues(
                            $cell,
                            $compareTo->rows[$rowIndex]->cells[$columnIndex]
                        )
                    );
            } else {
                // Handle removed rows
                $cellResults[$columnIndex] = new CellComparisonResult(
                    new StringComparisonResult([
                        new TokenStringDiff(
                            DiffStatus::REMOVED,
                            $cell->value
                        ),
                    ])
                );
            }
        }

        return $cellResults;
    }
}

class_alias(MatrixComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\MatrixComparisonEngine');
