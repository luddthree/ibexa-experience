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
use Ibexa\VersionComparison\Engine\Value\BinaryFileComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\BooleanComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\MediaComparisonResult;

final class MediaComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $integerComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\BinaryFileComparisonEngine */
    private $binaryFileComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\BooleanComparisonEngine */
    private $booleanComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine,
        IntegerComparisonEngine $integerComparisonEngine,
        BinaryFileComparisonEngine $binaryFileComparisonEngine,
        BooleanComparisonEngine $booleanComparisonEngine
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
        $this->integerComparisonEngine = $integerComparisonEngine;
        $this->binaryFileComparisonEngine = $binaryFileComparisonEngine;
        $this->booleanComparisonEngine = $booleanComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Media\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Media\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $nameResult = $this->stringComparisonEngine->compareValues(
            $comparisonDataA->fileName,
            $comparisonDataB->fileName
        );

        $fileSizeResult = $this->integerComparisonEngine->compareValues(
            $comparisonDataA->fileSize,
            $comparisonDataB->fileSize
        );

        $autoplayResult = $this->booleanComparisonEngine->compareValues(
            $comparisonDataA->autoplay,
            $comparisonDataB->autoplay
        );

        $hasControllerResult = $this->booleanComparisonEngine->compareValues(
            $comparisonDataA->hasController,
            $comparisonDataB->hasController
        );

        $loopResult = $this->booleanComparisonEngine->compareValues(
            $comparisonDataA->loop,
            $comparisonDataB->loop
        );

        $mimeTypeResult = $this->stringComparisonEngine->compareValues(
            $comparisonDataA->mimeType,
            $comparisonDataB->mimeType
        );

        $fileResult = $this->binaryFileComparisonEngine->compareValues(
            $comparisonDataA->file,
            $comparisonDataB->file
        );

        return new MediaComparisonResult(
            $nameResult,
            $fileSizeResult,
            $fileResult,
            $autoplayResult,
            $hasControllerResult,
            $loopResult,
            $mimeTypeResult
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Media\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Media\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return ($comparisonDataA->fileSize->value !== $comparisonDataB->fileSize->value)
            || ($comparisonDataA->fileName->value !== $comparisonDataB->fileName->value)
            || ($comparisonDataA->hasController->value !== $comparisonDataB->hasController->value)
            || ($comparisonDataA->autoplay->value !== $comparisonDataB->autoplay->value)
            || ($comparisonDataA->loop->value !== $comparisonDataB->loop->value)
            || ($comparisonDataA->mimeType->value !== $comparisonDataB->mimeType->value)
            || !$this->binaryFileComparisonEngine->areEqual($comparisonDataA->file, $comparisonDataB->file);
    }
}

class_alias(MediaComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\MediaComparisonEngine');
