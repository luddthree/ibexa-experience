<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff;

final class DateTimeComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff */
    private $from;

    /** @var \Ibexa\VersionComparison\Result\Value\Diff\DateTimeDiff */
    private $to;

    public function __construct(DateTimeDiff $from, DateTimeDiff $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): DateTimeDiff
    {
        return $this->from;
    }

    public function getTo(): DateTimeDiff
    {
        return $this->to;
    }

    public function isChanged(): bool
    {
        return $this->from->getStatus() !== DiffStatus::UNCHANGED
            || $this->to->getStatus() !== DiffStatus::UNCHANGED;
    }
}

class_alias(DateTimeComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\DateTimeComparisonResult');
