<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;

final class ProductCategory implements CriterionInterface
{
    /** @var int[] */
    private array $taxonomyEntries;

    /**
     * @param int[] $taxonomyEntries
     */
    public function __construct(array $taxonomyEntries)
    {
        $this->taxonomyEntries = $taxonomyEntries;
    }

    /**
     * @return int[]
     */
    public function getTaxonomyEntries(): array
    {
        return $this->taxonomyEntries;
    }
}
