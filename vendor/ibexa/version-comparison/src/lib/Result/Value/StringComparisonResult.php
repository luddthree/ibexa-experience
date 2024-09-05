<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value;

use ArrayIterator;
use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\DiffStatus;
use IteratorAggregate;

final class StringComparisonResult implements ComparisonResult, IteratorAggregate
{
    /** @var \Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff[] */
    private $tokenStringDiffs;

    /** @param \Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff[] $tokenStringDiffs */
    public function __construct(array $tokenStringDiffs)
    {
        $this->tokenStringDiffs = $tokenStringDiffs;
    }

    /**
     * @return \Ibexa\VersionComparison\Result\Value\Diff\TokenStringDiff[]
     */
    public function getTokenStringDiffs(): array
    {
        return $this->tokenStringDiffs;
    }

    public function isChanged(): bool
    {
        foreach ($this->tokenStringDiffs as $tokenStringDiff) {
            if ($tokenStringDiff->getStatus() !== DiffStatus::UNCHANGED) {
                return true;
            }
        }

        return false;
    }

    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->tokenStringDiffs);
    }
}

class_alias(StringComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\StringComparisonResult');
