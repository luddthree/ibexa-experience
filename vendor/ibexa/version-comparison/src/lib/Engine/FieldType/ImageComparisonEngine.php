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
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\ImageComparisonResult;

final class ImageComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\BinaryFileComparisonEngine */
    private $binaryFileComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine,
        BinaryFileComparisonEngine $binaryFileComparisonEngine
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
        $this->binaryFileComparisonEngine = $binaryFileComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Image\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Image\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $alternativeTextResult = $this->stringComparisonEngine->compareValues(
            $comparisonDataA->alternativeText,
            $comparisonDataB->alternativeText
        );
        $fileNameResult = $this->stringComparisonEngine->compareValues(
            $comparisonDataA->fileName,
            $comparisonDataB->fileName
        );
        $fileResult = $this->binaryFileComparisonEngine->compareValues(
            $comparisonDataA->file,
            $comparisonDataB->file
        );

        return new ImageComparisonResult(
            $fileNameResult,
            $alternativeTextResult,
            $fileResult
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Image\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Image\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return ($comparisonDataA->alternativeText->value !== $comparisonDataB->alternativeText->value)
            || ($comparisonDataA->fileName->value !== $comparisonDataB->fileName->value)
            || !$this->binaryFileComparisonEngine->areEqual($comparisonDataA->file, $comparisonDataB->file);
    }
}

class_alias(ImageComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\ImageComparisonEngine');
