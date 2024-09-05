<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogStatus as CatalogStatusCriterion;
use Ibexa\Contracts\Rest\Exceptions;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Ibexa\Rest\Input\BaseParser;

final class CatalogStatus extends BaseParser
{
    private const CATALOG_STATUS_CRITERION = 'CatalogStatusCriterion';

    /**
     * @param array<mixed> $data
     *
     * @throws \Ibexa\Contracts\Rest\Exceptions\Parser
     */
    public function parse(array $data, ParsingDispatcher $parsingDispatcher): CatalogStatusCriterion
    {
        if (!array_key_exists(self::CATALOG_STATUS_CRITERION, $data)) {
            throw new Exceptions\Parser('Invalid <' . self::CATALOG_STATUS_CRITERION . '> format');
        }

        return new CatalogStatusCriterion($data[self::CATALOG_STATUS_CRITERION]);
    }
}
