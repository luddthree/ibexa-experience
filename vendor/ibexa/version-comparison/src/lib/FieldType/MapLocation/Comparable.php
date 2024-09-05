<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\MapLocation;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\FloatComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\MapLocation\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new Value([
            'address' => new StringComparisonValue([
                'value' => $value->address,
            ]),
            'longitude' => new FloatComparisonValue([
                'value' => $value->longitude,
            ]),
            'latitude' => new FloatComparisonValue([
                'value' => $value->latitude,
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\MapLocation\Comparable');
