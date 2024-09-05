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
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\TextLineComparisonResult;

final class TextLineComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringValueComparisonEngine;

    public function __construct(StringComparisonEngine $stringValueComparisonEngine)
    {
        $this->stringValueComparisonEngine = $stringValueComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\TextLine\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\TextLine\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): ComparisonResult
    {
        return new TextLineComparisonResult(
            $this->stringValueComparisonEngine->compareValues($comparisonDataA->textLine, $comparisonDataB->textLine)
        );
    }

    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->textLine->value !== $comparisonDataB->textLine->value;
    }
}

class_alias(TextLineComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\TextLineComparisonEngine');
