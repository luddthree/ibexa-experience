<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Matrix;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\FieldTypeMatrix\FieldType\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        $rows = [];
        foreach ($value->getRows() as $row) {
            /** @var \Ibexa\FieldTypeMatrix\FieldType\Value\Row $row */
            $cells = [];
            foreach ($row->getCells() as $columnIndex => $cell) {
                $cells[$columnIndex] = new StringComparisonValue([
                    'value' => $cell,
                    'doNotSplit' => true,
                ]);
            }
            $rows[] = new Row([
                'cells' => $cells,
            ]);
        }

        return new Value([
            'rows' => $rows,
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Matrix\Comparable');
