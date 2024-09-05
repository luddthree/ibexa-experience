<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;

final class UpdateTaxonomyEntryEvent extends AfterEvent
{
    private TaxonomyEntry $updatedTaxonomyEntry;

    private TaxonomyEntry $taxonomyEntry;

    private TaxonomyEntryUpdateStruct $updateStruct;

    public function __construct(
        TaxonomyEntry $updatedTaxonomyEntry,
        TaxonomyEntry $taxonomyEntry,
        TaxonomyEntryUpdateStruct $updateStruct
    ) {
        $this->updatedTaxonomyEntry = $updatedTaxonomyEntry;
        $this->taxonomyEntry = $taxonomyEntry;
        $this->updateStruct = $updateStruct;
    }

    public function getUpdatedTaxonomyEntry(): TaxonomyEntry
    {
        return $this->updatedTaxonomyEntry;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    public function getUpdateStruct(): TaxonomyEntryUpdateStruct
    {
        return $this->updateStruct;
    }
}
