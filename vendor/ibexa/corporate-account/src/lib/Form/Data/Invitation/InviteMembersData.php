<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Invitation;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Symfony\Component\Validator\Constraints as Assert;

final class InviteMembersData extends ValueObject
{
    /**
     * @var \Ibexa\CorporateAccount\Form\Data\Invitation\SimpleInvitationData[]
     *
     * @Assert\Valid()
     */
    private array $invitations;

    /** @return \Ibexa\CorporateAccount\Form\Data\Invitation\SimpleInvitationData[] */
    public function getInvitations(): array
    {
        return $this->invitations;
    }

    /** @param \Ibexa\CorporateAccount\Form\Data\Invitation\SimpleInvitationData[] $invitations */
    public function setInvitations(array $invitations): void
    {
        $this->invitations = $invitations;
    }
}
