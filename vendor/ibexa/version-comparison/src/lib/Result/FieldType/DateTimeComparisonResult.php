<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult as ValueDateTimeComparisonResult;
use Ibexa\VersionComparison\Result\Value\IntegerComparisonResult as ValueIntegerComparisonResult;

final class DateTimeComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\DateTimeComparisonResult */
    private $dateComparisonResult;

    /** @var \Ibexa\VersionComparison\Result\Value\IntegerComparisonResult */
    private $timeComparisonResult;

    public function __construct(
        ValueDateTimeComparisonResult $dateTimeComparisonResult,
        ValueIntegerComparisonResult $timeComparisonResult
    ) {
        $this->dateComparisonResult = $dateTimeComparisonResult;
        $this->timeComparisonResult = $timeComparisonResult;
    }

    public function getDateComparisonResult(): ValueDateTimeComparisonResult
    {
        return $this->dateComparisonResult;
    }

    public function getTimeComparisonResult(): ValueIntegerComparisonResult
    {
        return $this->timeComparisonResult;
    }

    public function isChanged(): bool
    {
        return $this->dateComparisonResult->isChanged() || $this->timeComparisonResult->isChanged();
    }
}

class_alias(DateTimeComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\DateTimeComparisonResult');
