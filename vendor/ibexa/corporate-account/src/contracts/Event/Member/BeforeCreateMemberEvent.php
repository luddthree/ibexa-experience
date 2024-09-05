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
use UnexpectedValueException;

final class BeforeCreateMemberEvent extends Event
{
    private Company $company;

    private MemberCreateStruct $memberCreateStruct;

    private Role $role;

    private ?Member $member = null;

    public function __construct(
        Company $company,
        MemberCreateStruct $memberCreateStruct,
        Role $role
    ) {
        $this->company = $company;
        $this->memberCreateStruct = $memberCreateStruct;
        $this->role = $role;
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

    public function getMember(): Member
    {
        if (!$this->hasMember()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasMember() or set it using setMember() before you call the getter.', Member::class));
        }

        return $this->member;
    }

    public function setMember(?Member $member): void
    {
        $this->member = $member;
    }

    public function hasMember(): bool
    {
        return $this->member instanceof Member;
    }
}
