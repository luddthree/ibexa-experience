<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

class TaxonomyEntryMoveData
{
    private ?TaxonomyEntry $entry;

    private ?TaxonomyEntry $newParent;

    public function __construct(?TaxonomyEntry $entry = null, ?TaxonomyEntry $newParent = null)
    {
        $this->entry = $entry;
        $this->newParent = $newParent;
    }

    public function getEntry(): ?TaxonomyEntry
    {
        return $this->entry;
    }

    public function setEntry(?TaxonomyEntry $entry): void
    {
        $this->entry = $entry;
    }

    public function getNewParent(): ?TaxonomyEntry
    {
        return $this->newParent;
    }

    public function setNewParent(?TaxonomyEntry $newParent): void
    {
        $this->newParent = $newParent;
    }
}
