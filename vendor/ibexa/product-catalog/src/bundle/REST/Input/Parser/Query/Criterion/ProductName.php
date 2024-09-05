<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductName as ProductNameCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductName extends BaseParser implements ProductCriterionInterface
{
    private const PRODUCT_NAME_CRITERION = 'ProductNameCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductNameCriterion
    {
        if (!array_key_exists(self::PRODUCT_NAME_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::PRODUCT_NAME_CRITERION . '> format');
        }

        return new ProductNameCriterion($data[self::PRODUCT_NAME_CRITERION]);
    }

    public function getName(): string
    {
        return self::PRODUCT_NAME_CRITERION;
    }
}
