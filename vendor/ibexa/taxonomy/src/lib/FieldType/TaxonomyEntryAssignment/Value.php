<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment;

use Ibexa\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    private ?string $taxonomy;

    /** @var array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> */
    private array $taxonomyEntries;

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $taxonomyEntries
     */
    public function __construct(
        array $taxonomyEntries = [],
        ?string $taxonomy = null
    ) {
        $this->taxonomy = $taxonomy;
        $this->taxonomyEntries = $taxonomyEntries;
    }

    public function getTaxonomy(): ?string
    {
        return $this->taxonomy;
    }

    public function setTaxonomy(string $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
    }

    /**
     * @return array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry>
     */
    public function getTaxonomyEntries(): array
    {
        return $this->taxonomyEntries;
    }

    /**
     * @param array<\Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry> $taxonomyEntries
     */
    public function setTaxonomyEntries(array $taxonomyEntries): void
    {
        $this->taxonomyEntries = $taxonomyEntries;
    }

    public function __toString(): string
    {
        if (empty($this->taxonomyEntries)) {
            return '';
        }

        $entries = $this->taxonomyEntries;
        /** @var \Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry $firstEntry */
        $firstEntry = array_shift($entries);
        $name = $firstEntry->name;

        return sprintf('%s and %d more', $name, count($entries));
    }
}
