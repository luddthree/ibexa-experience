<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff;

final class IntegerComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff */
    private $from;

    /** @var \Ibexa\VersionComparison\Result\Value\Diff\IntegerDiff */
    private $to;

    public function __construct(IntegerDiff $from, IntegerDiff $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function getFrom(): IntegerDiff
    {
        return $this->from;
    }

    public function getTo(): IntegerDiff
    {
        return $this->to;
    }

    public function isChanged(): bool
    {
        return $this->from->getStatus() !== DiffStatus::UNCHANGED
            || $this->to->getStatus() !== DiffStatus::UNCHANGED;
    }
}

class_alias(IntegerComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\IntegerComparisonResult');
