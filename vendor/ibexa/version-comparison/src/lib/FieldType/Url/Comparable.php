<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\Url;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\StringComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\Url\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new Value([
            'link' => new StringComparisonValue([
                'value' => $value->link,
                'doNotSplit' => true,
            ]),
            'text' => new StringComparisonValue([
                'value' => $value->text,
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\Url\Comparable');
