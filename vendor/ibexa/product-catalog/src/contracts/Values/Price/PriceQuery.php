<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery;

/**
 * @phpstan-extends \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Price\Query\SortClauseInterface & \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause,
 *     \Ibexa\Contracts\ProductCatalog\Values\Price\Query\CriterionInterface & \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
 * >
 */
final class PriceQuery extends AbstractCriterionQuery
{
}
