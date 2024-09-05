<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\CustomerPortal\PickRule;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\Values\Member;

interface CustomerPortalPickRule
{
    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Location[] $possibleLocations
     */
    public function pick(Member $member, array $possibleLocations): ?Location;
}
