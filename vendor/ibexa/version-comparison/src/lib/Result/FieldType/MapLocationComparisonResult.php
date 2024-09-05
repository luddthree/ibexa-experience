<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\FloatComparisonResult as ValueFloatComparisonResult;
use Ibexa\VersionComparison\Result\Value\StringComparisonResult;

final class MapLocationComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\StringComparisonResult */
    private $addressComparisonResult;

    /** @var \Ibexa\VersionComparison\Result\Value\FloatComparisonResult */
    private $longitudeComparisonResult;

    /** @var \Ibexa\VersionComparison\Result\Value\FloatComparisonResult */
    private $latitudeComparisonResult;

    public function __construct(
        StringComparisonResult $addressComparisonResult,
        ValueFloatComparisonResult $longitudeComparisonResult,
        ValueFloatComparisonResult $latitudeComparisonResult
    ) {
        $this->addressComparisonResult = $addressComparisonResult;
        $this->longitudeComparisonResult = $longitudeComparisonResult;
        $this->latitudeComparisonResult = $latitudeComparisonResult;
    }

    public function getAddressComparisonResult(
    ): StringComparisonResult {
        return $this->addressComparisonResult;
    }

    public function getLongitudeComparisonResult(
    ): ValueFloatComparisonResult {
        return $this->longitudeComparisonResult;
    }

    public function getLatitudeComparisonResult(
    ): ValueFloatComparisonResult {
        return $this->latitudeComparisonResult;
    }

    public function isChanged(): bool
    {
        return
            $this->addressComparisonResult->isChanged()
            || $this->longitudeComparisonResult->isChanged()
            || $this->latitudeComparisonResult->isChanged();
    }
}

class_alias(MapLocationComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\MapLocationComparisonResult');
