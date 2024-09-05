<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

class TaxonomyEntryDeleteData
{
    private ?TaxonomyEntry $entry;

    public function __construct(?TaxonomyEntry $entry = null)
    {
        $this->entry = $entry;
    }

    public function getEntry(): ?TaxonomyEntry
    {
        return $this->entry;
    }

    public function setEntry(?TaxonomyEntry $entry): void
    {
        $this->entry = $entry;
    }
}
