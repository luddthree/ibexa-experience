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
use Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\ImageAssetComparisonResult;

final class ImageAssetComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $integerComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\BinaryFileComparisonEngine */
    private $fileComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine,
        IntegerComparisonEngine $integerComparisonEngine,
        BinaryFileComparisonEngine $fileComparisonEngine
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
        $this->integerComparisonEngine = $integerComparisonEngine;
        $this->fileComparisonEngine = $fileComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\ImageAsset\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\ImageAsset\Value $comparisonDataB
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
        $destinationIdResult = $this->integerComparisonEngine->compareValues(
            $comparisonDataA->destinationContentId,
            $comparisonDataB->destinationContentId
        );
        $fileResult = $this->fileComparisonEngine->compareValues(
            $comparisonDataA->file,
            $comparisonDataB->file
        );

        return new ImageAssetComparisonResult(
            $fileNameResult,
            $alternativeTextResult,
            $destinationIdResult,
            $fileResult
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\ImageAsset\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\ImageAsset\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return ($comparisonDataA->alternativeText->value !== $comparisonDataB->alternativeText->value)
            || ($comparisonDataA->fileName->value !== $comparisonDataB->fileName->value)
            || ($comparisonDataA->destinationContentId->value !== $comparisonDataB->destinationContentId->value)
            || !$this->fileComparisonEngine->areEqual($comparisonDataA->file, $comparisonDataB->file);
    }
}

class_alias(ImageAssetComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\ImageAssetComparisonEngine');
