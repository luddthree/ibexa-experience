<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\StepExecutor\ActionExecutor;

use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Migration\StepExecutor\ActionExecutor\ExecutorInterface;
use Ibexa\Segmentation\Value\Step\Action\AbstractAssignUser;

abstract class AbstractAssignToUser implements ExecutorInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    protected function getUser(AbstractAssignUser $action): User
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
