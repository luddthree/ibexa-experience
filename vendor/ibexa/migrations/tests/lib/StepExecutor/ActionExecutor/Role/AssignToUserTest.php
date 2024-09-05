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
use Ibexa\Migration\StepExecutor\ActionExecutor\Role\AssignToUser;
use Ibexa\Migration\ValueObject\Step\Action;
use Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser as AssignToUserAction;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Migration\StepExecutor\ActionExecutor\Role\AssignToUser
 */
final class AssignToUserTest extends TestCase
{
    /** @var \Ibexa\Migration\StepExecutor\ActionExecutor\Role\AssignToUser */
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

        $this->executor = new AssignToUser($userService, $this->roleService);
    }

    /**
     * @dataProvider providerForCanOnlyWorkOnAssignToUserObjects
     */
    public function testCanOnlyWorkOnAssignToUserObjects(AssignToUserAction $action): void
    {
        $this->roleService->expects(self::once())
            ->method('assignRoleToUser');

        $this->executor->handle($action, $this->createMock(Role::class));
    }

    /**
     * @return iterable<array{\Ibexa\Migration\ValueObject\Step\Action\Role\AssignToUser}>
     */
    public function providerForCanOnlyWorkOnAssignToUserObjects(): iterable
    {
        yield [
            new AssignToUserAction(42),
        ];

        yield [
            new AssignToUserAction(null, '42'),
        ];

        yield [
            new AssignToUserAction(null, null, 'foo'),
        ];
    }

    public function testThrowsRuntimeExceptionWhenNonAssignToUserObjectIsPassed(): void
    {
        $this->roleService->expects(self::never())
            ->method('assignRoleToUser');

        $action = $this->createMock(Action::class);

        $this->expectException(InvalidArgumentException::class);
        $this->executor->handle($action, $this->createMock(Role::class));
    }
}

class_alias(AssignToUserTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\ActionExecutor\Role\AssignToUserTest');
