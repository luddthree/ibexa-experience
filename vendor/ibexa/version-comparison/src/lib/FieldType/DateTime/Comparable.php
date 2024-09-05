<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\FieldType\DateTime;

use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\VersionComparison\FieldType\Comparable as ComparableInterface;
use Ibexa\Contracts\VersionComparison\FieldType\FieldTypeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\DateTimeComparisonValue;
use Ibexa\VersionComparison\ComparisonValue\IntegerComparisonValue;

final class Comparable implements ComparableInterface
{
    /**
     * @param \Ibexa\Core\FieldType\DateAndTime\Value $value
     */
    public function getDataToCompare(SPIValue $value): FieldTypeComparisonValue
    {
        $dateTime = $value->value;

        return new Value([
            'dateTime' => new DateTimeComparisonValue([
                'value' => $this->stripTimeFromDateTime($dateTime),
            ]),
            'time' => new IntegerComparisonValue([
                'value' => $this->convertTimeToSeconds($dateTime),
            ]),
        ]);
    }

    private function stripTimeFromDateTime(?\DateTime $dateTime): ?\DateTime
    {
        if ($dateTime === null) {
            return null;
        }

        return new \DateTime($dateTime->format('Y-m-d'), $dateTime->getTimezone());
    }

    private function convertTimeToSeconds(?\DateTime $dateTime): ?int
    {
        if ($dateTime === null) {
            return null;
        }

        return (int)$dateTime->format('G') * 3600
            + (int)$dateTime->format('i') * 60
            + (int)$dateTime->format('s');
    }
}

class_alias(Comparable::class, 'EzSystems\EzPlatformVersionComparison\FieldType\DateTime\Comparable');
