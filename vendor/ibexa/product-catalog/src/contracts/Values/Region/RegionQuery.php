<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Region;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery;

/**
 * @phpstan-extends \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractSortClause,
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
 * >
 */
final class RegionQuery extends AbstractCriterionQuery
{
}
