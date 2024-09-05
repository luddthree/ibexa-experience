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
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser as AssignToUserAction;
use Webmozart\Assert\Assert;

final class AssignToUser implements ExecutorInterface
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
        return AssignToUserAction::TYPE;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\Action|\Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser $action
     * @param \Ibexa\Contracts\Core\Repository\Values\ValueObject|\Ibexa\Contracts\Core\Repository\Values\User\Role $valueObject
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\LimitationValidationException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    public function handle(Action $action, ValueObject $valueObject): void
    {
        Assert::isInstanceOf($action, AssignToUserAction::class);
        Assert::isInstanceOf($valueObject, Role::class);

        $user = $this->getUser($action);
        $limitation = $action->getLimitation();
        $this->roleService->assignRoleToUser($valueObject, $user, $limitation);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getUser(AssignToUserAction $action): User
    {
        $id = $action->getId();
        if ($id !== null) {
            return $this->userService->loadUser($id);
        }

        $login = $action->getLogin();
        if ($login !== null) {
            return $this->userService->loadUserByLogin($login);
        }

        $email = $action->getEmail();
        if ($email !== null) {
            return $this->userService->loadUserByEmail($email);
        }

        throw new InvalidArgumentException(
            '$action',
            'Action object does not contain ID, email nor login identifier',
        );
    }
}

class_alias(AssignToUser::class, 'Ibexa\Platform\Migration\StepExecutor\ActionExecutor\Role\AssignToUser');
