<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

use Ibexa\Contracts\ProductCatalog\Values\Product\ProductVariantQuery as ProductVariantQueryValueObject;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

/**
 * @extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\AbstractQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\CriterionInterface,
 *     \Ibexa\Contracts\ProductCatalog\Values\Product\Query\SortClause,
 * >
 */
final class ProductVariantQuery extends AbstractQuery
{
    protected function getAllowedKeys(): array
    {
        return [];
    }

    /**
     * @param array<mixed> $data
     */
    protected function buildQuery(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): ProductVariantQueryValueObject {
        return new ProductVariantQueryValueObject();
    }
}
