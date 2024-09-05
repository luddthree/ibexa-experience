<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Time;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\Time\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new Value([
            'time' => new IntegerComparisonValue([
                'value' => $value->time,
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Time\Comparable');
