<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult;
use Ibexa\VersionComparison\Result\Value\BooleanComparisonResult;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

class MediaComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $nameResult;

    /** @var \Ibexa\VersionComparison\Result\Value\IntegerComparisonResult */
    private $fileSizeResult;

    /** @var \Ibexa\VersionComparison\Result\Value\BinaryFileComparisonResult */
    private $fileResult;

    /** @var \Ibexa\VersionComparison\Result\Value\BooleanComparisonResult */
    private $autoplayResult;

    /** @var \Ibexa\VersionComparison\Result\Value\BooleanComparisonResult */
    private $hasControllerResult;

    /** @var \Ibexa\VersionComparison\Result\Value\BooleanComparisonResult */
    private $loopResult;

    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $mimeTypeResult;

    public function __construct(
        StringComparisonResult $nameResult,
        IntegerComparisonResult $fileSizeResult,
        BinaryFileComparisonResult $fileResult,
        BooleanComparisonResult $autoplayResult,
        BooleanComparisonResult $hasControllerResult,
        BooleanComparisonResult $loopResult,
        StringComparisonResult $mimeTypeResult
    ) {
        $this->nameResult = $nameResult;
        $this->fileSizeResult = $fileSizeResult;
        $this->fileResult = $fileResult;
        $this->autoplayResult = $autoplayResult;
        $this->hasControllerResult = $hasControllerResult;
        $this->loopResult = $loopResult;
        $this->mimeTypeResult = $mimeTypeResult;
    }

    public function getFileNameComparisonResult(): StringComparisonResult
    {
        return $this->nameResult;
    }

    public function getFileSizeComparisonResult(): IntegerComparisonResult
    {
        return $this->fileSizeResult;
    }

    public function getFileComparisonResult(): BinaryFileComparisonResult
    {
        return $this->fileResult;
    }

    public function getAutoplayComparisonResult(): BooleanComparisonResult
    {
        return $this->autoplayResult;
    }

    public function getHasControllerComparisonResult(): BooleanComparisonResult
    {
        return $this->hasControllerResult;
    }

    public function getLoopComparisonResult(): BooleanComparisonResult
    {
        return $this->loopResult;
    }

    public function getMimeTypeComparisonResult(): StringComparisonResult
    {
        return $this->mimeTypeResult;
    }

    public function isChanged(): bool
    {
        return $this->nameResult->isChanged()
             || $this->fileSizeResult->isChanged()
             || $this->fileResult->isChanged()
             || $this->autoplayResult->isChanged()
             || $this->hasControllerResult->isChanged()
             || $this->loopResult->isChanged()
             || $this->mimeTypeResult->isChanged();
    }
}

class_alias(MediaComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\MediaComparisonResult');
