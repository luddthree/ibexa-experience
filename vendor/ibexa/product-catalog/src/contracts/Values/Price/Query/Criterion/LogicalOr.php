<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\ProductCatalog\Values\Price\Query\Criterion;

use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\LogicalOr as BaseLogicalOr;
use Ibexa\Contracts\ProductCatalog\Values\Price\Query\CriterionInterface;

final class LogicalOr extends BaseLogicalOr implements CriterionInterface
{
}
