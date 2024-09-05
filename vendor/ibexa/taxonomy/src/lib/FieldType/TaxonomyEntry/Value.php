<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntry;

use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;
use Ibexa\Core\FieldType\Value as BaseValue;

class Value extends BaseValue
{
    protected ?TaxonomyEntry $taxonomyEntry;

    public function __construct(?TaxonomyEntry $taxonomyEntry = null)
    {
        parent::__construct([
            'taxonomyEntry' => $taxonomyEntry,
        ]);
    }

    public function getTaxonomyEntry(): ?TaxonomyEntry
    {
        return $this->taxonomyEntry;
    }

    public function setTaxonomyEntry(?TaxonomyEntry $taxonomyEntry): void
    {
        $this->taxonomyEntry = $taxonomyEntry;
    }

    public function __toString(): string
    {
        return $this->taxonomyEntry->identifier ?? '';
    }
}
