<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Member;

use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\CorporateAccount\Values\Company;
use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberCreateStruct;
use Symfony\Contracts\EventDispatcher\Event;

final class CreateMemberEvent extends Event
{
    private Member $member;

    private Company $company;

    private MemberCreateStruct $memberCreateStruct;

    private Role $role;

    public function __construct(
        Member $member,
        Company $company,
        MemberCreateStruct $memberCreateStruct,
        Role $role
    ) {
        $this->member = $member;
        $this->company = $company;
        $this->memberCreateStruct = $memberCreateStruct;
        $this->role = $role;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getMemberCreateStruct(): MemberCreateStruct
    {
        return $this->memberCreateStruct;
    }

    public function getRole(): Role
    {
        return $this->role;
    }
}
