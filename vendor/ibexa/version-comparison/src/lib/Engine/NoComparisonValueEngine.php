<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Engine;

use Ibexa\Contracts\VersionComparison\Engine\FieldTypeComparisonEngine;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\NoComparisonResult;

final class NoComparisonValueEngine implements FieldTypeComparisonEngine
{
    public function compareFieldsTypeValues(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): ComparisonResult
    {
        return new NoComparisonResult();
    }

    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return true;
    }
}

class_alias(NoComparisonValueEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\NoComparisonValueEngine');
