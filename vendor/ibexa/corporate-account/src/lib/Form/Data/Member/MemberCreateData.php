<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Form\Data\Member;

use Ibexa\ContentForms\Data\User\UserCreateData;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Symfony\Component\Validator\Constraints as Assert;

class MemberCreateData extends UserCreateData
{
    /** @Assert\NotBlank() */
    private ?Role $role;

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): void
    {
        $this->role = $role;
    }
}
