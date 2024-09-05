<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use ArrayIterator;
use Ibexa\Contracts\CorporateAccount\Values\SalesRepresentativesList as APISalesRepresentativesList;
use Ibexa\Rest\Value as RestValue;
use IteratorAggregate;

/**
 * @internal
 */
final class SalesRepresentativesList extends RestValue implements IteratorAggregate
{
    private APISalesRepresentativesList $salesRepresentativesList;

    public function __construct(APISalesRepresentativesList $salesRepresentativesList)
    {
        $this->salesRepresentativesList = $salesRepresentativesList;
    }

    public function getSalesRepresentativesList(): APISalesRepresentativesList
    {
        return $this->salesRepresentativesList;
    }

    public function getIterator(): ArrayIterator
    {
        return $this->salesRepresentativesList->getIterator();
    }
}
