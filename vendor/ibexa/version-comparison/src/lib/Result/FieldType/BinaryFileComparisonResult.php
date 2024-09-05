<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class BinaryFileComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $fileNameDiff;

    /** @var \Ibexa\VersionComparison\Result\Value\IntegerComparisonResult */
    private $fileSizeDiff;

    public function __construct(
        StringComparisonResult $fileNameDiff,
        IntegerComparisonResult $fileSizeDiff
    ) {
        $this->fileNameDiff = $fileNameDiff;
        $this->fileSizeDiff = $fileSizeDiff;
    }

    public function getFileNameComparisonResult(): StringComparisonResult
    {
        return $this->fileNameDiff;
    }

    public function getFileSizeComparisonResult(): IntegerComparisonResult
    {
        return $this->fileSizeDiff;
    }

    public function isChanged(): bool
    {
        return $this->fileNameDiff->isChanged() || $this->fileSizeDiff->isChanged();
    }
}

class_alias(BinaryFileComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\BinaryFileComparisonResult');
