<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

final class SelectionComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\CollectionComparisonResult */
    private $comparisonResult;

    public function __construct(CollectionComparisonResult $comparisonResult)
    {
        $this->comparisonResult = $comparisonResult;
    }

    public function getRemovedSelection(): CollectionDiff
    {
        return $this->comparisonResult->getRemoved();
    }

    public function getUnchangedSelection(): CollectionDiff
    {
        return $this->comparisonResult->getUnchanged();
    }

    public function getAddedSelection(): CollectionDiff
    {
        return $this->comparisonResult->getAdded();
    }

    public function isChanged(): bool
    {
        return $this->comparisonResult->isChanged();
    }
}

class_alias(SelectionComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\SelectionComparisonResult');
