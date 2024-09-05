<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Data;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductsPreviewFormData
{
    private ?ProductListFormData $search = null;

    private ?CriterionInterface $filters = null;

    public function getSearch(): ?ProductListFormData
    {
        return $this->search;
    }

    public function setSearch(?ProductListFormData $search): void
    {
        $this->search = $search;
    }

    public function getFilters(): ?CriterionInterface
    {
        return $this->filters;
    }

    public function setFilters(?CriterionInterface $filters): void
    {
        $this->filters = $filters;
    }
}
