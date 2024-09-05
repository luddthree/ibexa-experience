<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\ProductCriterionAdapter;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface as ProductCriterion;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface;

/**
 * @implements \Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalNot
 * >
 */
final class LogicalNotCriterionConverter implements CriterionConverterInterface
{
    /**
     * @param \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalNot $criterion
     */
    public function convert(ProductCriterion $criterion): ContentCriterion
    {
        return new ContentCriterion\LogicalNot(new ProductCriterionAdapter($criterion->getCriterion()));
    }
}
