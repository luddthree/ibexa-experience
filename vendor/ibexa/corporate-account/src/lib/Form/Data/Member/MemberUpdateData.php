<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Member;

use Ibexa\ContentForms\Data\User\UserUpdateData;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\CorporateAccount\Values\Member;

class MemberUpdateData extends UserUpdateData
{
    private Member $member;

    private Role $role;

    public function getMember(): Member
    {
        return $this->member;
    }

    public function setMember(Member $member): void
    {
        $this->member = $member;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }
}
