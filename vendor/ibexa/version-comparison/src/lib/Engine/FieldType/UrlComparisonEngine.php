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
use Ibexa\VersionComparison\Result\FieldType\UrlComparisonResult;

final class UrlComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringValueComparisonEngine;

    public function __construct(StringComparisonEngine $stringValueComparisonEngine)
    {
        $this->stringValueComparisonEngine = $stringValueComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\Url\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\Url\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        return new UrlComparisonResult(
            $this->stringValueComparisonEngine->compareValues($comparisonDataA->link, $comparisonDataB->link),
            $this->stringValueComparisonEngine->compareValues($comparisonDataA->text, $comparisonDataB->text)
        );
    }

    public function shouldRunComparison(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): bool {
        return ($comparisonDataA->text->value !== $comparisonDataB->text->value)
            || ($comparisonDataA->link->value !== $comparisonDataB->link->value);
    }
}

class_alias(UrlComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\UrlComparisonEngine');
