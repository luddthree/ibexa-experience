<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\REST\Value;

use Ibexa\Contracts\CorporateAccount\Values\Member as APIMember;
use Ibexa\Rest\Value as RestValue;

/**
 * @internal
 */
final class Member extends RestValue
{
    private APIMember $member;

    public function __construct(APIMember $member)
    {
        $this->member = $member;
    }

    public function getMember(): APIMember
    {
        return $this->member;
    }
}
