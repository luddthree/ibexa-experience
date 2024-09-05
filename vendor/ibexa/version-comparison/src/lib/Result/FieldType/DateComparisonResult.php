<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult as ValueDateTimeComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff;

final class DateComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult */
    private $comparisonResult;

    public function __construct(ValueDateTimeComparisonResult $comparisonResult)
    {
        $this->comparisonResult = $comparisonResult;
    }

    public function getFrom(): DateTimeDiff
    {
        return $this->comparisonResult->getFrom();
    }

    public function getTo(): DateTimeDiff
    {
        return $this->comparisonResult->getTo();
    }

    public function isChanged(): bool
    {
        return $this->comparisonResult->isChanged();
    }
}

class_alias(DateComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\DateComparisonResult');
