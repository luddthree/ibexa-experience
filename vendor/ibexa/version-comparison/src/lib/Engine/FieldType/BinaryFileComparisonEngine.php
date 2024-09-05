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
use Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\BinaryFileComparisonResult;

final class BinaryFileComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\IntegerComparisonEngine */
    private $integerComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine,
        IntegerComparisonEngine $integerComparisonEngine
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
        $this->integerComparisonEngine = $integerComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\BinaryFile\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\BinaryFile\Value $comparisonDataB
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

        return new BinaryFileComparisonResult(
            $nameResult,
            $fileSizeResult
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\BinaryFile\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\BinaryFile\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return ($comparisonDataA->fileSize->value !== $comparisonDataB->fileSize->value)
            || ($comparisonDataA->fileName->value !== $comparisonDataB->fileName->value);
    }
}

class_alias(BinaryFileComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\BinaryFileComparisonEngine');
