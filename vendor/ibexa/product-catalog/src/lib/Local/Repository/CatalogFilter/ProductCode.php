<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\CatalogFilter;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode as ProductCodeCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface;
use JMS\TranslationBundle\Annotation\Desc;

final class ProductCode extends StandardDefinition
{
    public function getIdentifier(): string
    {
        return 'product_code';
    }

    public function getName(): string
    {
        return $this->translator->trans(
            /** @Desc("Code") */
            'filter.product_code.label',
            [],
            'ibexa_product_catalog'
        );
    }

    public function supports(CriterionInterface $criterion): bool
    {
        return $criterion instanceof ProductCodeCriterion;
    }
}
