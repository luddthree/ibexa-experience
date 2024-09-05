<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType\Matrix;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\Core\Base\Exceptions\NotFoundException;

final class RowComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult[] */
    private $cellsComparisonResults;

    /**
     * @param \Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult[] $cellsComparisonResults
     */
    public function __construct(array $cellsComparisonResults)
    {
        $this->cellsComparisonResults = $cellsComparisonResults;
    }

    public function isChanged(): bool
    {
        foreach ($this->cellsComparisonResults as $cellComparisonResult) {
            if ($cellComparisonResult->isChanged()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \Ibexa\VersionComparison\Result\FieldType\Matrix\CellComparisonResult[]
     */
    public function getCellsComparisonResults(): array
    {
        return $this->cellsComparisonResults;
    }

    public function haveCellComparisonResult(string $identifier): bool
    {
        return \array_key_exists($identifier, $this->cellsComparisonResults);
    }

    public function getCellComparisonResult(string $identifier): ?CellComparisonResult
    {
        if (!\array_key_exists($identifier, $this->cellsComparisonResults)) {
            throw new NotFoundException('Cell Comparison Result', $identifier);
        }

        return $this->cellsComparisonResults[$identifier];
    }
}

class_alias(RowComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\Matrix\RowComparisonResult');
