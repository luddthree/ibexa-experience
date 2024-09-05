<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterionInterface;

/**
 * Bridge criterion between Product Catalog and Content Repository.
 *
 * @phpstan-template T of ProductCriterionInterface
 */
final class ProductCriterionAdapter extends Criterion
{
    /**
     * @phpstan-var T
     */
    private ProductCriterionInterface $criterion;

    /**
     * @phpstan-param T $criterion
     */
    public function __construct(ProductCriterionInterface $criterion)
    {
        $this->criterion = $criterion;
    }

    /**
     * @return \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications[]
     */
    public function getSpecifications(): array
    {
        return [];
    }

    /**
     * @phpstan-return T
     */
    public function getProductCriterion(): ProductCriterionInterface
    {
        return $this->criterion;
    }
}
