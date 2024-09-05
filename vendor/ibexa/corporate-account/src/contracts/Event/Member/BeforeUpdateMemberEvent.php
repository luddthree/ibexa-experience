<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Member;

use Ibexa\Contracts\CorporateAccount\Values\Member;
use Ibexa\Contracts\CorporateAccount\Values\MemberUpdateStruct;
use Symfony\Contracts\EventDispatcher\Event;
use UnexpectedValueException;

final class BeforeUpdateMemberEvent extends Event
{
    private Member $member;

    private MemberUpdateStruct $memberUpdateStruct;

    private ?Member $updatedMember = null;

    public function __construct(
        Member $member,
        MemberUpdateStruct $memberUpdateStruct
    ) {
        $this->member = $member;
        $this->memberUpdateStruct = $memberUpdateStruct;
    }

    public function getMember(): Member
    {
        return $this->member;
    }

    public function getMemberUpdateStruct(): MemberUpdateStruct
    {
        return $this->memberUpdateStruct;
    }

    public function getUpdatedMember(): Member
    {
        if (!$this->hasUpdatedMember()) {
            throw new UnexpectedValueException(sprintf('Return value is not set or not of type %s. Check hasUpdatedMember() or set it using setUpdatedMember() before you call the getter.', Member::class));
        }

        return $this->updatedMember;
    }

    public function setUpdatedMember(?Member $updatedMember): void
    {
        $this->updatedMember = $updatedMember;
    }

    public function hasUpdatedMember(): bool
    {
        return $this->updatedMember instanceof Member;
    }
}
