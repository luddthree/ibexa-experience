<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Taxonomy\Event;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryCreateStruct;

final class CreateTaxonomyEntryEvent extends AfterEvent
{
    private TaxonomyEntry $taxonomyEntry;

    private TaxonomyEntryCreateStruct $createStruct;

    public function __construct(
        TaxonomyEntry $taxonomyEntry,
        TaxonomyEntryCreateStruct $createStruct
    ) {
        $this->taxonomyEntry = $taxonomyEntry;
        $this->createStruct = $createStruct;
    }

    public function getTaxonomyEntry(): TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    public function getCreateStruct(): TaxonomyEntryCreateStruct
    {
        return $this->createStruct;
    }
}
