<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;

/**
 * Converts product criterion to content criterion.
 *
 * @template T of ProductCriterion
 */
interface CriterionConverterInterface
{
    /**
     * @param T $criterion
     */
    public function convert(ProductCriterion $criterion): ContentCriterion;
}
