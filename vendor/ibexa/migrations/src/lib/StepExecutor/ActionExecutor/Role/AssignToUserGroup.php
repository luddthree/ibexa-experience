<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\Role;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup as AssignToUserGroupAction;
use Webmozart\Assert\Assert;

final class AssignToUserGroup implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    public function __construct(UserService $userService, RoleService $roleService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
    }

    public static function getExecutorKey(): string
    {
        return AssignToUserGroupAction::TYPE;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action|\Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\Role $valueObject
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, ValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignToUserGroupAction::class);
        Assert::isInstanceOf($valueObject, Role::class);

        $userGroup = $this->getGroup($action);
        $limitation = $action->getLimitation();
        $this->roleService->assignRoleToUserGroup($valueObject, $userGroup, $limitation);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getGroup(AssignToUserGroupAction $action): UserGroup
    {
        $id = $action->getId();
        if ($id !== null) {
            return $this->userService->loadUserGroup($id);
        }

        $remoteId = $action->getRemoteId();
        if ($remoteId !== null) {
            return $this->userService->loadUserGroupByRemoteId($remoteId);
        }

        throw new InvalidArgumentException(
            '$action',
            'Action object does not contain ID nor remoteId',
        );
    }
}

class_alias(AssignToUserGroup::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\Role\AssignToUserGroup');
