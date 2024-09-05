<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;

abstract class AbstractUserGroupStepExecutor extends AbstractStepExecutor
{
    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @param mixed[] $roleIdentifiers
     */
    protected function assignRoleToUserGroup(UserGroup $userGroup, array $roleIdentifiers): void
    {
        foreach ($roleIdentifiers as $roleIdentifier) {
            $role = $this->loadRole($roleIdentifier);
            $this->roleService->assignRoleToUserGroup($role, $userGroup);
        }
    }

    /**
     * @param int|string $roleIdentifier
     */
    protected function loadRole($roleIdentifier): Role
    {
        if (is_numeric($roleIdentifier)) {
            return $this->roleService->loadRole((int) $roleIdentifier);
        }

        return $this->roleService->loadRoleByIdentifier($roleIdentifier);
    }
}

class_alias(AbstractUserGroupStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\AbstractUserGroupStepExecutor');
