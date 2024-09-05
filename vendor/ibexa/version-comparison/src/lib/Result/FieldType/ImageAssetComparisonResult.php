<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class ImageAssetComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $fileNameResult;

    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $alternativeTextResult;

    /** @var \Ibexa\VersionComparison\Result\Value\IntegerComparisonResult */
    private $destinationIdResult;

    /** @var \Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult */
    private $binaryFileComparisonResult;

    public function __construct(
        StringComparisonResult $fileNameResult,
        StringComparisonResult $alternativeTextResult,
        IntegerComparisonResult $destinationIdResult,
        BinaryFileComparisonResult $binaryFileComparisonResult
    ) {
        $this->fileNameResult = $fileNameResult;
        $this->alternativeTextResult = $alternativeTextResult;
        $this->destinationIdResult = $destinationIdResult;
        $this->binaryFileComparisonResult = $binaryFileComparisonResult;
    }

    public function getFileNameComparisonResult(): StringComparisonResult
    {
        return $this->fileNameResult;
    }

    public function getAlternativeTextComparisonResult(): StringComparisonResult
    {
        return $this->alternativeTextResult;
    }

    public function getDestinationIdComparisonResult(): IntegerComparisonResult
    {
        return $this->destinationIdResult;
    }

    public function getBinaryFileComparisonResult(): BinaryFileComparisonResult
    {
        return $this->binaryFileComparisonResult;
    }

    public function isChanged(): bool
    {
        return
            $this->fileNameResult->isChanged()
            || $this->alternativeTextResult->isChanged()
            || $this->destinationIdResult->isChanged()
            || $this->binaryFileComparisonResult->isChanged();
    }
}

class_alias(ImageAssetComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\ImageAssetComparisonResult');
