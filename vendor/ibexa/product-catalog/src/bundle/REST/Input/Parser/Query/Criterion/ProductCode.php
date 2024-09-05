<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode as ProductCodeCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class ProductCode extends BaseParser implements ProductCriterionInterface
{
    private const PRODUCT_CODE_CRITERION = 'ProductCodeCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): ProductCodeCriterion
    {
        if (!array_key_exists(self::PRODUCT_CODE_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::PRODUCT_CODE_CRITERION . '> format');
        }

        $codes = $data[self::PRODUCT_CODE_CRITERION];
        if (!is_array($codes)) {
            $codes = [$codes];
        }

        return new ProductCodeCriterion($codes);
    }

    public function getName(): string
    {
        return self::PRODUCT_CODE_CRITERION;
    }
}
