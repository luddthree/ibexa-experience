<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult as ValueIntegerComparisonResult;

final class TimeComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\IntegerComparisonResult */
    private $comparisonResult;

    public function __construct(ValueIntegerComparisonResult $comparisonResult)
    {
        $this->comparisonResult = $comparisonResult;
    }

    public function getFrom(): IntegerDiff
    {
        return $this->comparisonResult->getFrom();
    }

    public function getTo(): IntegerDiff
    {
        return $this->comparisonResult->getTo();
    }

    public function isChanged(): bool
    {
        return $this->comparisonResult->isChanged();
    }
}

class_alias(TimeComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\TimeComparisonResult');
