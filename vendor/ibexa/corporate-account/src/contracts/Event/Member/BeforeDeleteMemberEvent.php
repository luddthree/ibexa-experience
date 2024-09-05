<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Event\Member;

use Ibexa\Contracts\CorporateAccount\Values\Member;
use Symfony\Contracts\EventDispatcher\Event;

final class BeforeDeleteMemberEvent extends Event
{
    private Member $member;

    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function getMember(): Member
    {
        return $this->member;
    }
}
