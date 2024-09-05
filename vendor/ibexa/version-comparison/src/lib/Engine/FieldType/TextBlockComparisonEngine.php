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
use Ibexa\VersionComparison\Result\FieldType\TextBlockComparisonResult;

final class TextBlockComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringValueComparisonEngine;

    public function __construct(StringComparisonEngine $stringValueComparisonEngine)
    {
        $this->stringValueComparisonEngine = $stringValueComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\TextBlock\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\TextBlock\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): ComparisonResult
    {
        return new TextBlockComparisonResult(
            $this->stringValueComparisonEngine->compareValues($comparisonDataA->textBlock, $comparisonDataB->textBlock)
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\TextBlock\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\TextBlock\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return $comparisonDataA->textBlock->value !== $comparisonDataB->textBlock->value;
    }
}

class_alias(TextBlockComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\TextBlockComparisonEngine');
