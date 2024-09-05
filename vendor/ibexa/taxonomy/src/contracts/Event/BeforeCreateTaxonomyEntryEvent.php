<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;
use UnexpectedValueException;

final class BeforeCreateTaxonomyEntryEvent extends BeforeEvent
{
    private TaxonomyEntryCreateStruct $createStruct;

    private ?TaxonomyEntry $taxonomyEntry;

    public function __construct(
        TaxonomyEntryCreateStruct $createStruct,
        ?TaxonomyEntry $taxonomyEntry = null
    ) {
        $this->createStruct = $createStruct;
        $this->taxonomyEntry = $taxonomyEntry;
    }

    public function getCreateStruct(): TaxonomyEntryCreateStruct
    {
        return $this->createStruct;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        if (!$this->hasTaxonomyEntry()) {
            throw new UnexpectedValueException(
                sprintf(
                    'Return value is not set or not of type %s. Check hasTaxonomyEntry() or '
                    . 'set it using setTaxonomyEntry() before you call the getter.',
                    TaxonomyEntry::class
                )
            );
        }

        return $this->taxonomyEntry;
    }

    public function setTaxonomyEntry(?TaxonomyEntry $taxonomyEntry): void
    {
        $this->taxonomyEntry = $taxonomyEntry;
    }

    public function hasTaxonomyEntry(): bool
    {
        return $this->taxonomyEntry instanceof TaxonomyEntry;
    }
}
