<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\CustomerPortal;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\CorporateAccount\Values\Member;

interface CustomerPortalResolver
{
    public function resolveCustomerPortalLocation(Member $member, string $siteAccessName): ?Location;
}
