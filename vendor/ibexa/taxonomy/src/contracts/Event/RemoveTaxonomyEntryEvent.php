<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class RemoveTaxonomyEntryEvent extends AfterEvent
{
    private TaxonomyEntry $taxonomyEntry;

    /** @var array<int> */
    private array $contentIds = [];

    /**
     * @param array<int> $contentIds
     */
    public function __construct(
        TaxonomyEntry $taxonomyEntry,
        array $contentIds = []
    ) {
        $this->taxonomyEntry = $taxonomyEntry;
        $this->contentIds = $contentIds;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    /**
     * @return array<int>
     */
    public function getContentIds(): array
    {
        return $this->contentIds;
    }
}
