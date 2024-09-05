<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;

class NonComparable implements Comparable
{
    public const FIELD_TYPE_ALIAS = 'eznoncomparable';

    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new NoComparison();
    }
}

class_alias(NonComparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\NonComparable');
