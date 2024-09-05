<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class TaxonomyContentAssignData
{
    private ?TaxonomyEntry $entry;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Location[] */
    private array $locations;

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $locations
     */
    public function __construct(
        ?TaxonomyEntry $entry = null,
        array $locations = []
    ) {
        $this->entry = $entry;
        $this->locations = $locations;
    }

    public function getEntry(): ?TaxonomyEntry
    {
        return $this->entry;
    }

    public function setEntry(?TaxonomyEntry $entry): void
    {
        $this->entry = $entry;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Location[]
     */
    public function getLocations(): array
    {
        return $this->locations;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $locations
     */
    public function setLocations(array $locations): void
    {
        $this->locations = $locations;
    }
}
