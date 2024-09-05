<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\VersionComparison\FieldType;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;

interface Comparable
{
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue;
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Comparable');
