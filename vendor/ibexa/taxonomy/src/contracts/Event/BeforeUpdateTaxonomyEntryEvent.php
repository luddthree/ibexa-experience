<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryUpdateStruct;
use UnexpectedValueException;

final class BeforeUpdateTaxonomyEntryEvent extends BeforeEvent
{
    private TaxonomyEntry $taxonomyEntry;

    private TaxonomyEntryUpdateStruct $updateStruct;

    private ?TaxonomyEntry $updatedTaxonomyEntry;

    public function __construct(
        TaxonomyEntry $taxonomyEntry,
        TaxonomyEntryUpdateStruct $updateStruct,
        ?TaxonomyEntry $updatedTaxonomyEntry = null
    ) {
        $this->taxonomyEntry = $taxonomyEntry;
        $this->updateStruct = $updateStruct;
        $this->updatedTaxonomyEntry = $updatedTaxonomyEntry;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    public function getUpdateStruct(): TaxonomyEntryUpdateStruct
    {
        return $this->updateStruct;
    }

    public function getUpdatedTaxonomyEntry(): TaxonomyEntry
    {
        if (!$this->hasUpdatedTaxonomyEntry()) {
            throw new UnexpectedValueException(
                sprintf(
                    'Return value is not set or not of type %s. Check hasUpdatedTaxonomyEntry() or '
                    . 'set it using setUpdatedTaxonomyEntry() before you call the getter.',
                    TaxonomyEntry::class
                )
            );
        }

        return $this->updatedTaxonomyEntry;
    }

    public function setUpdatedTaxonomyEntry(?TaxonomyEntry $taxonomyEntry): void
    {
        $this->updatedTaxonomyEntry = $taxonomyEntry;
    }

    public function hasUpdatedTaxonomyEntry(): bool
    {
        return $this->updatedTaxonomyEntry instanceof TaxonomyEntry;
    }
}
