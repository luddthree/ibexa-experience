<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\FloatDiff;
use Ibexa\VersionComparison\Result\Value\FloatComparisonResult as ValueFloatComparisonResult;

final class FloatComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\FloatComparisonResult */
    private $comparisonResult;

    public function __construct(ValueFloatComparisonResult $comparisonResult)
    {
        $this->comparisonResult = $comparisonResult;
    }

    public function getFrom(): FloatDiff
    {
        return $this->comparisonResult->getFrom();
    }

    public function getTo(): FloatDiff
    {
        return $this->comparisonResult->getTo();
    }

    public function isChanged(): bool
    {
        return $this->comparisonResult->isChanged();
    }
}

class_alias(FloatComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\FloatComparisonResult');
