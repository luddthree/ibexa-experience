<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType\Matrix;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class CellComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $stringComparisonResult;

    public function __construct(StringComparisonResult $stringComparisonResult)
    {
        $this->stringComparisonResult = $stringComparisonResult;
    }

    public function isChanged(): bool
    {
        return $this->stringComparisonResult->isChanged();
    }

    public function getStringComparisonResult(): StringComparisonResult
    {
        return $this->stringComparisonResult;
    }
}

class_alias(CellComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\Matrix\CellComparisonResult');
