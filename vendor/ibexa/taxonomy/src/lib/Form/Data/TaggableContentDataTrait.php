<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

trait TaggableContentDataTrait
{
    /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] */
    private array $taxonomyEntries;

    /**
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[]
     */
    public function getTaxonomyEntries(): array
    {
        return $this->taxonomyEntries;
    }

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $taxonomyEntries
     */
    public function setTaxonomyEntries(array $taxonomyEntries): void
    {
        $this->taxonomyEntries = $taxonomyEntries;
    }
}
