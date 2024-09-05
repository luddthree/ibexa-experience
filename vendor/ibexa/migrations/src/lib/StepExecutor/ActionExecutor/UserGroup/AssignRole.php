<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor\ActionExecutor\UserGroup;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\UserGroup;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\UserGroup\AssignRole as AssignRoleAction;
use Webmozart\Assert\Assert;

final class AssignRole implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\RoleService */
    private $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public static function getExecutorKey(): string
    {
        return AssignRoleAction::TYPE;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action|\Ibexa\Migration\ValueObject\Step\Action\UserGroup\AssignRole $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\UserGroup $valueObject
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, ValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignRoleAction::class);
        Assert::isInstanceOf($valueObject, UserGroup::class);

        $role = $this->getRole($action);
        $limitation = $action->getLimitation();
        $this->roleService->assignRoleToUserGroup($role, $valueObject, $limitation);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getRole(AssignRoleAction $action): Role
    {
        $id = $action->getId();
        if ($id !== null) {
            return $this->roleService->loadRole($id);
        }

        $identifier = $action->getIdentifier();
        if ($identifier !== null) {
            return $this->roleService->loadRoleByIdentifier($identifier);
        }

        throw new InvalidArgumentException(
            '$action',
            'Action object does not contain ID nor identifier',
        );
    }
}

class_alias(AssignRole::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\UserGroup\AssignRole');
