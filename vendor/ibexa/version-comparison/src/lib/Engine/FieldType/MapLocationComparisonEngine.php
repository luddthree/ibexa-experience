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
use Ibexa\VersionComparison\Engine\Value\FloatComparisonEngine;
use Ibexa\VersionComparison\Engine\Value\StringComparisonEngine;
use Ibexa\VersionComparison\Result\FieldType\MapLocationComparisonResult;

final class MapLocationComparisonEngine implements FieldTypeComparisonEngine
{
    /** @var \Ibexa\VersionComparison\Engine\Value\StringComparisonEngine */
    private $stringComparisonEngine;

    /** @var \Ibexa\VersionComparison\Engine\Value\FloatComparisonEngine */
    private $floatComparisonEngine;

    public function __construct(
        StringComparisonEngine $stringComparisonEngine,
        FloatComparisonEngine $floatComparisonEngine
    ) {
        $this->stringComparisonEngine = $stringComparisonEngine;
        $this->floatComparisonEngine = $floatComparisonEngine;
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\MapLocation\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\MapLocation\Value $comparisonDataB
     */
    public function compareFieldsTypeValues(
        FieldTypeComparisonValue $comparisonDataA,
        FieldTypeComparisonValue $comparisonDataB
    ): ComparisonResult {
        $addressStringResult = $this->stringComparisonEngine->compareValues(
            $comparisonDataA->address,
            $comparisonDataB->address
        );
        $longitudeResult = $this->floatComparisonEngine->compareValues(
            $comparisonDataA->longitude,
            $comparisonDataB->longitude
        );
        $latitudeResult = $this->floatComparisonEngine->compareValues(
            $comparisonDataA->latitude,
            $comparisonDataB->latitude
        );

        return new MapLocationComparisonResult(
            $addressStringResult,
            $longitudeResult,
            $latitudeResult
        );
    }

    /**
     * @param \Ibexa\VersionComparison\FieldType\MapLocation\Value $comparisonDataA
     * @param \Ibexa\VersionComparison\FieldType\MapLocation\Value $comparisonDataB
     */
    public function shouldRunComparison(FieldTypeComparisonValue $comparisonDataA, FieldTypeComparisonValue $comparisonDataB): bool
    {
        return ($comparisonDataA->address->value !== $comparisonDataB->address->value)
            || ($comparisonDataA->latitude->value !== $comparisonDataB->latitude->value)
            || ($comparisonDataA->longitude->value !== $comparisonDataB->longitude->value);
    }
}

class_alias(MapLocationComparisonEngine::class, 'EzSystems\EzPlatformVersionComparison\Engine\FieldType\MapLocationComparisonEngine');
