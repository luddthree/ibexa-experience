<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\SalesRepresentatives;

use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;

interface SalesRepresentativesFilterBuilderInterface
{
    public function buildFilterForGetAllQuery(int $offset, int $limit): Filter;
}
