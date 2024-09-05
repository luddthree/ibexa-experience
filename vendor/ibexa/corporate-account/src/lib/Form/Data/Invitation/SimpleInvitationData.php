<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Invitation;

use Ibexa\Core\Repository\Values\User\Role;
use Ibexa\User\Validator\Constraints as UserAssert;
use Symfony\Component\Validator\Constraints as Assert;

final class SimpleInvitationData
{
    /**
     * @Assert\NotNull()
     *
     * @Assert\Email()
     *
     * @UserAssert\EmailInvitation()
     */
    private ?string $email;

    /**
     * @Assert\NotNull()
     */
    private ?Role $role;

    public function __construct(?string $email = null, ?Role $role = null)
    {
        $this->email = $email;
        $this->role = $role;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function setRole(?Role $role): void
    {
        $this->role = $role;
    }
}
