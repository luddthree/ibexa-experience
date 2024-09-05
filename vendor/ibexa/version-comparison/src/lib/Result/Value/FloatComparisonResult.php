<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\FloatDiff;

final class FloatComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\Diff\FloatDiff */
    private $from;

    /** @var \Ibexa\VersionComparison\Result\Value\Diff\FloatDiff */
    private $to;

    public function __construct(FloatDiff $from, FloatDiff $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): FloatDiff
    {
        return $this->from;
    }

    public function getTo(): FloatDiff
    {
        return $this->to;
    }

    public function isChanged(): bool
    {
        return $this->from->getStatus() !== DiffStatus::UNCHANGED
            || $this->to->getStatus() !== DiffStatus::UNCHANGED;
    }
}

class_alias(FloatComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\FloatComparisonResult');
