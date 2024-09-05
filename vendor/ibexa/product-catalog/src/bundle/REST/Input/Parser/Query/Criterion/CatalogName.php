<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogName as CatalogNameCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CatalogName extends BaseParser
{
    private const CATALOG_NAME_CRITERION = 'CatalogNameCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CatalogNameCriterion
    {
        if (!array_key_exists(self::CATALOG_NAME_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::CATALOG_NAME_CRITERION . '> format');
        }

        return new CatalogNameCriterion($data[self::CATALOG_NAME_CRITERION]);
    }
}
