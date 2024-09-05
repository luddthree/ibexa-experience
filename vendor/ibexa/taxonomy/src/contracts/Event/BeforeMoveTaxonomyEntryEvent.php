<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class BeforeMoveTaxonomyEntryEvent extends BeforeEvent
{
    private TaxonomyEntry $taxonomyEntry;

    private TaxonomyEntry $newParent;

    public function __construct(
        TaxonomyEntry $taxonomyEntry,
        TaxonomyEntry $newParent
    ) {
        $this->taxonomyEntry = $taxonomyEntry;
        $this->newParent = $newParent;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    public function getNewParent(): TaxonomyEntry
    {
        return $this->newParent;
    }
}
