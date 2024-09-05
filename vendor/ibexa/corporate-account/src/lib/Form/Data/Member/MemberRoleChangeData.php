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
use Symfony\Component\Validator\Constraints as Assert;

final class MemberRoleChangeData extends UserUpdateData
{
    /** @Assert\NotBlank() */
    private ?Member $member;

    /** @Assert\NotBlank() */
    private ?Role $newRole;

    public function getMember(): ?Member
    {
        return $this->member;
    }

    public function setMember(Member $member): void
    {
        $this->member = $member;
    }

    public function getNewRole(): ?Role
    {
        return $this->newRole;
    }

    public function setNewRole(?Role $newRole): void
    {
        $this->newRole = $newRole;
    }
}
