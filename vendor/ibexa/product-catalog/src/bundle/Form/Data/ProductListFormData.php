<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntry;

final class ProductListFormData extends SearchQueryData
{
    private ?SortClause $sortClause = null;

    private ?TaxonomyEntry $category = null;

    public function getSortClause(): ?SortClause
    {
        return $this->sortClause;
    }

    public function setSortClause(?SortClause $sortClause): void
    {
        $this->sortClause = $sortClause;
    }

    public function getCategory(): ?TaxonomyEntry
    {
        return $this->category;
    }

    public function setCategory(?TaxonomyEntry $category): void
    {
        $this->category = $category;
    }
}
