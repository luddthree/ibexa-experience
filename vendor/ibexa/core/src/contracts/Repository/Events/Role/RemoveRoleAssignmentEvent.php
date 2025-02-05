<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Core\Repository\Events\Role;

use Ibexa\Contracts\Core\Repository\Event\AfterEvent;
use Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment;

final class RemoveRoleAssignmentEvent extends AfterEvent
{
    /** @var \Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment */
    private $roleAssignment;

    public function __construct(
        RoleAssignment $roleAssignment
    ) {
        $this->roleAssignment = $roleAssignment;
    }

    public function getRoleAssignment(): RoleAssignment
    {
        return $this->roleAssignment;
    }
}

class_alias(RemoveRoleAssignmentEvent::class, 'eZ\Publish\API\Repository\Events\Role\RemoveRoleAssignmentEvent');
