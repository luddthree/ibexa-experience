<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCategory as ProductCategoryCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductCategory extends BaseParser implements ProductCriterionInterface
{
    private const PRODUCT_CATEGORY_CRITERION = 'ProductCategoryCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductCategoryCriterion
    {
        if (!array_key_exists(self::PRODUCT_CATEGORY_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::PRODUCT_CATEGORY_CRITERION . '> format');
        }

        return new ProductCategoryCriterion($data[self::PRODUCT_CATEGORY_CRITERION]);
    }

    public function getName(): string
    {
        return self::PRODUCT_CATEGORY_CRITERION;
    }
}
