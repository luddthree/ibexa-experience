<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Invitation;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\MVC\Symfony\SiteAccess;
use Ibexa\CorporateAccount\Validation\Constraints\IsCorporateSiteAccess;
use Symfony\Component\Validator\Constraints as Assert;

final class InviteMembersWithSiteAccessData extends ValueObject
{
    /**
     * @var \Ibexa\CorporateAccount\Form\Data\Invitation\SimpleInvitationData[]
     *
     * @Assert\Valid()
     */
    private array $invitations;

    /**
     * @IsCorporateSiteAccess
     */
    private SiteAccess $siteAccess;

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

    public function getSiteAccess(): SiteAccess
    {
        return $this->siteAccess;
    }

    public function setSiteAccess(SiteAccess $siteAccess): void
    {
        $this->siteAccess = $siteAccess;
    }
}
