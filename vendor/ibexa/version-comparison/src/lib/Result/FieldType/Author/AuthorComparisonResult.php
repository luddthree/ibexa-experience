<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\VersionComparison\Result\FieldType\Author;

use Ibexa\Contracts\VersionComparison\Result\ComparisonResult;
use Ibexa\VersionComparison\Result\Value\CollectionComparisonResult;
use Ibexa\VersionComparison\Result\Value\Diff\CollectionDiff;

final class AuthorComparisonResult implements ComparisonResult
{
    /** @var \Ibexa\VersionComparison\Result\Value\CollectionComparisonResult */
    private $authorCollection;

    public function __construct(CollectionComparisonResult $authorCollection)
    {
        $this->authorCollection = $authorCollection;
    }

    public function getRemovedAuthors(): CollectionDiff
    {
        return $this->authorCollection->getRemoved();
    }

    public function getRetainedAuthors(): CollectionDiff
    {
        return $this->authorCollection->getUnchanged();
    }

    public function getAddedAuthors(): CollectionDiff
    {
        return $this->authorCollection->getAdded();
    }

    public function isChanged(): bool
    {
        $isCollectionChanged = $this->authorCollection->isChanged();
        $areAuthorsChanged = false;

        /** @var \Ibexa\VersionComparison\Result\FieldType\Author\SingleAuthorComparisonResult $authorDiff */
        foreach ($this->getRetainedAuthors()->getCollection() as $authorDiff) {
            if ($authorDiff->isChanged()) {
                $areAuthorsChanged = true;
            }
        }

        return $isCollectionChanged || $areAuthorsChanged;
    }
}

class_alias(AuthorComparisonResult::class, 'EzSystems\EzPlatformVersionComparison\Result\FieldType\Author\AuthorComparisonResult');
