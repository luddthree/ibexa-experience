<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison\Engine;

use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;

interface FieldTypeComparisonEngine
{
    public function compareFieldsTypeValues(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): ComparisonResult;

    /**
     * As running comparison could be memory and time consuming, you have the option to preliminary
     * decide if it should be run at all, based on entry data.
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool;
}

class_alias(FieldTypeComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldTypeComparisonEngine');
