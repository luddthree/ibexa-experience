<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverter;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion as ContentCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use Ibexa\Contracts\Taxonomy\Search\Query\Criterion\TaxonomySubtree;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface;

/**
 * @implements \Ibexa\ProductCatalog\Local\Repository\Search\Common\CriterionConverterInterface<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategorySubtree
 * >
 */
final class ProductCategorySubtreeCriterionConverter implements CriterionConverterInterface
{
    public function convert(CriterionInterface $criterion): ContentCriterion
    {
        return new TaxonomySubtree($criterion->getTaxonomyEntryId());
    }
}
