<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query;

use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery as AttributeGroupQueryValueObject;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;

/**
 * @extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\Query\AbstractQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause,
 * >
 */
final class AttributeGroupQuery extends AbstractQuery
{
    protected function getAllowedKeys(): array
    {
        return ['name_prefix'];
    }

    /**
     * @param array<mixed> $data
     */
    protected function buildQuery(
        array $data,
        ParsingDispatcher $parsingDispatcher
    ): AttributeGroupQueryValueObject {
        $query = new AttributeGroupQueryValueObject();

        if (array_key_exists('name_prefix', $data)) {
            $query->setNamePrefix($data['name_prefix']);
        }

        return $query;
    }
}
