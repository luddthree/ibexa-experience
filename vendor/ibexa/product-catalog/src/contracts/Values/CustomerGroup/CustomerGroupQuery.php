<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery;

/**
 * @phpstan-extends \Ibexa\Contracts\ProductCatalog\Values\Common\Query\AbstractCriterionQuery<
 *     \Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\SortClause,
 *     \Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\CriterionInterface,
 * >
 */
final class CustomerGroupQuery extends AbstractCriterionQuery
{
}
