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
use Ibexa\VersionComparison\Engine\Value\HtmlComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\RichTextComparisonResult;

final class RichTextComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\HtmlComparisonEngine */
    private $htmlComparisonEngine;

    public function __construct(HtmlComparisonEngine $htmlComparisonEngine)
    {
        $this->htmlComparisonEngine = $htmlComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\RichText\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\RichText\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        return new RichTextComparisonResult(
            $this->htmlComparisonEngine->compareValues($comparisonDataA->html, $comparisonDataB->html)
        );
    }

    public function shouldRunComparison(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): bool {
        return $comparisonDataA->html->value !== $comparisonDataB->html->value;
    }
}

class_alias(RichTextComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\RichTextComparisonEngine');
