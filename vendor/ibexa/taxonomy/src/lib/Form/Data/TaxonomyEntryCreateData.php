<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\AdminUi\Form\Data\Content\Draft\ContentCreateData;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

class TaxonomyEntryCreateData extends ContentCreateData
{
    private ?TaxonomyEntry $parentEntry;

    public function __construct(
        ?ContentType $contentType = null,
        ?Location $parentLocation = null,
        ?Language $language = null,
        ?TaxonomyEntry $parentEntry = null
    ) {
        parent::__construct($contentType, $parentLocation, $language);

        $this->parentEntry = $parentEntry;
    }

    public function getParentEntry(): ?TaxonomyEntry
    {
        return $this->parentEntry;
    }

    public function setParentEntry(TaxonomyEntry $parentEntry): void
    {
        $this->parentEntry = $parentEntry;
    }
}
