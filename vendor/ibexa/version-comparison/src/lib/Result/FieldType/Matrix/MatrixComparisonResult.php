<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType\Matrix;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;

final class MatrixComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult[] */
    private $rowsComparisonResult;

    /**
     * @param \Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult[] $rowsComparisonResult
     */
    public function __construct(array $rowsComparisonResult)
    {
        $this->rowsComparisonResult = $rowsComparisonResult;
    }

    public function isChanged(): bool
    {
        foreach ($this->rowsComparisonResult as $rowComparisonResult) {
            if ($rowComparisonResult->isChanged()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Ibexa\VersionComparison\Result\FieldType\Matrix\RowComparisonResult[]
     */
    public function getRowsComparisonResult(): array
    {
        return $this->rowsComparisonResult;
    }
}

class_alias(MatrixComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\Matrix\MatrixComparisonResult');
