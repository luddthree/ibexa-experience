<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType as ProductTypeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductType extends BaseParser
{
    private const PRODUCT_TYPE_CRITERION = 'ProductTypeCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductTypeCriterion
    {
        if (!array_key_exists(self::PRODUCT_TYPE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::PRODUCT_TYPE_CRITERION . '> format');
        }

        return new ProductTypeCriterion(explode(',', $data[self::PRODUCT_TYPE_CRITERION]));
    }
}
