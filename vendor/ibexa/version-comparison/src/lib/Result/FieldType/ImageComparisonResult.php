<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class ImageComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $fileNameComparisonResult;

    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $alternativeTextComparisonResult;

    /** @var \Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult */
    private $binaryFileComparisonResult;

    public function __construct(
        StringComparisonResult $fileNameComparisonResult,
        StringComparisonResult $alternativeTextComparisonResult,
        BinaryFileComparisonResult $binaryFileComparisonResult
    ) {
        $this->fileNameComparisonResult = $fileNameComparisonResult;
        $this->alternativeTextComparisonResult = $alternativeTextComparisonResult;
        $this->binaryFileComparisonResult = $binaryFileComparisonResult;
    }

    public function getFileNameComparisonResult(): StringComparisonResult
    {
        return $this->fileNameComparisonResult;
    }

    public function getAlternativeTextComparisonResult(): StringComparisonResult
    {
        return $this->alternativeTextComparisonResult;
    }

    public function getBinaryFileComparisonResult(): BinaryFileComparisonResult
    {
        return $this->binaryFileComparisonResult;
    }

    public function isChanged(): bool
    {
        return
            $this->fileNameComparisonResult->isChanged()
            || $this->alternativeTextComparisonResult->isChanged()
            || $this->binaryFileComparisonResult->isChanged();
    }
}

class_alias(ImageComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\ImageComparisonResult');
