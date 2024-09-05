<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor\ActionExecutor\Role;

use Ibexa\Contracts\Core\Repository\RoleService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\Role;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Migration\StepExecutor\ActionExecutor\Role\AssignToUserGroup;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup as AssignToUserGroupAction;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\StepExecutor\ActionExecutor\Role\AssignToUser
 */
final class AssignToUserGroupTest extends TestCase
{
    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\Role\AssignToUserGroup */
    private $executor;

    /** @var \Ibexa\Contracts\Core\Repository\RoleService|\PHPUnit\Framework\MockObject\MockObject */
    private $roleService;

    protected function setUp(): void
    {
        $user = $this->createMock(User::class);

        $userService = $this->createMock(UserService::class);
        $userService->method('loadUser')
            ->willReturn($user);

        $this->roleService = $this->createMock(RoleService::class);

        $this->executor = new AssignToUserGroup($userService, $this->roleService);
    }

    /**
     * @dataProvider providerForCanOnlyWorkOnAssignToUserObjects
     */
    public function testCanOnlyWorkOnAssignToUserObjects(AssignToUserGroupAction $action): void
    {
        $this->roleService->expects(self::once())
            ->method('assignRoleToUserGroup');

        $this->executor->handle($action, $this->createMock(Role::class));
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUserGroup}>
     */
    public function providerForCanOnlyWorkOnAssignToUserObjects(): iterable
    {
        yield [
            new AssignToUserGroupAction(42),
        ];

        yield [
            new AssignToUserGroupAction(null, '42'),
        ];
    }

    public function testThrowsRuntimeExceptionWhenNonAssignToUserObjectIsPassed(): void
    {
        $this->roleService->expects(self::never())
            ->method('assignRoleToUserGroup');

        $action = $this->createMock(Action::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->executor->handle($action, $this->createMock(Role::class));
    }
}

class_alias(AssignToUserGroupTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ActionExecutor\Role\AssignToUserGroupTest');
