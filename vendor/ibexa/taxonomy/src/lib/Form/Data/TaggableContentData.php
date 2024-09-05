<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Form\Data;

interface TaggableContentData
{
    /**
     * @return \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[]
     */
    public function getTaxonomyEntries(): array;

    /**
     * @param \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry[] $taxonomyEntries
     */
    public function setTaxonomyEntries(array $taxonomyEntries): void;
}
