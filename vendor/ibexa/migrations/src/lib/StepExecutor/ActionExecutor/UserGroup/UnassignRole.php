<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\UserGroup;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\RoleAssignment;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\UserGroup\UnassignRole as UnassignRoleAction;
use Webmozart\Assert\Assert;

final class UnassignRole implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function getExecutorKey(): string
    {
        return UnassignRoleAction::TYPE;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action|\Ibexa\Migration\ValueObject\Step\Action\UserGroup\UnassignRole $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $valueObject
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, ValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, UnassignRoleAction::class);
        Assert::isInstanceOf($valueObject, UserGroup::class);

        $roleAssignment = $this->roleService->loadRoleAssignment($action->getId());

        if (!$this->userGroupHasRole($valueObject, $roleAssignment)) {
            return;
        }

        $this->roleService->removeRoleAssignment($roleAssignment);
    }

    private function userGroupHasRole(UserGroup $userGroup, RoleAssignment $userGroupHasRole): bool
    {
        $roleAssignmentsForUserGroup = $this->roleService->getRoleAssignmentsForUserGroup($userGroup);
        $role = $userGroupHasRole->getRole();

        foreach ($roleAssignmentsForUserGroup as $roleAssignmentForUserGroup) {
            if ($roleAssignmentForUserGroup->getRole()->id === $role->id) {
                return true;
            }
        }

        return false;
    }
}

class_alias(UnassignRole::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\UserGroup\UnassignRole');
