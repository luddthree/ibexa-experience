<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery as CustomerGroupQueryValueObject;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

/**
 * @extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\AbstractQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause,
 * >
 */
final class CustomerGroupQuery extends AbstractQuery
{
    protected function getAllowedKeys(): array
    {
        return [self::QUERY];
    }

    /**
     * @param array<mixed> $data
     */
    protected function buildQuery(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): CustomerGroupQueryValueObject {
        $query = new CustomerGroupQueryValueObject();

        if (array_key_exists(self::QUERY, $data) && is_array($data[self::QUERY])) {
            $criteria = $this->processCriteriaArray($data[self::QUERY], $parsingDispatcher);
            $query->setQuery(
                new LogicalAnd(...$criteria)
            );
        }

        return $query;
    }
}
