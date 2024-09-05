<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Member;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeSetMemberRoleEvent extends Event
{
    private Member $member;

    private Role $role;

    public function __construct(
        Member $member,
        Role $role
    ) {
        $this->member = $member;
        $this->role = $role;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}
