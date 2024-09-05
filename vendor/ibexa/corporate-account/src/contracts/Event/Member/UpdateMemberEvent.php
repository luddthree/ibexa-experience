<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Member;

use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct;

final class UpdateMemberEvent
{
    private Member $updatedMember;

    private Member $member;

    private MemberUpdateStruct $memberUpdateStruct;

    public function __construct(
        Member $updatedMember,
        Member $member,
        MemberUpdateStruct $memberUpdateStruct
    ) {
        $this->updatedMember = $updatedMember;
        $this->member = $member;
        $this->memberUpdateStruct = $memberUpdateStruct;
    }

    public function getUpdatedMember(): Member
    {
        return $this->updatedMember;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getMemberUpdateStruct(): MemberUpdateStruct
    {
        return $this->memberUpdateStruct;
    }
}
