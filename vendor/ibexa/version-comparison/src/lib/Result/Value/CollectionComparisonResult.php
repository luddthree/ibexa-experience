<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\Value;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

final class CollectionComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff */
    private $removed;

    /** @var \Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff */
    private $unchanged;

    /** @var \Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff */
    private $added;

    public function __construct(CollectionDiff $removed, CollectionDiff $unchanged, CollectionDiff $added)
    {
        $this->removed = $removed;
        $this->unchanged = $unchanged;
        $this->added = $added;
    }

    public function getRemoved(): CollectionDiff
    {
        return $this->removed;
    }

    public function getUnchanged(): CollectionDiff
    {
        return $this->unchanged;
    }

    public function getAdded(): CollectionDiff
    {
        return $this->added;
    }

    public function isChanged(): bool
    {
        return !empty($this->removed->getCollection()) || !empty($this->added->getCollection());
    }
}

class_alias(CollectionComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\Value\CollectionComparisonResult');
