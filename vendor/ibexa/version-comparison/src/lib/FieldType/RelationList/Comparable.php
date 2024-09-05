<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\RelationList;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\Collection;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\RelationList\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        return new Value([
            'relationIdList' => new Collection([
                'collection' => $value->destinationContentIds,
            ]),
        ]);
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\RelationList\Comparable');
