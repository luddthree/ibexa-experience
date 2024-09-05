<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\User\Limitation\SectionLimitation;
use Ibexa\Contracts\Core\Repository\Values\User\UserRoleAssignment;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandRoleCreateWithActionsTest extends AbstractMigrateCommandRoleCreateTest
{
    private const IDENTIFIER = 'foo';
    private const USER_LOGIN = '__foo__';
    private const LIMITATION_VALUES = ['1'];

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/role-create-with-actions.yaml');
    }

    protected function postCommandAssertions(): void
    {
        $roleService = self::getRoleService();
        $userService = self::getUserService();

        $user = $userService->loadUserByLogin(self::USER_LOGIN);
        $role = $roleService->loadRoleByIdentifier(self::IDENTIFIER);

        self::assertSame(self::IDENTIFIER, $role->identifier);

        $roleService = self::getRoleService();
        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);

        self::assertCount(1, $roleAssignments);

        $roleAssignment = current($roleAssignments);

        self::assertInstanceOf(UserRoleAssignment::class, $roleAssignment);

        $roleIdentifiers = $roleAssignment->getRole()->identifier;

        self::assertSame(self::IDENTIFIER, $roleIdentifiers);

        $roleLimitation = $roleAssignment->getRoleLimitation();
        $userRole = $roleAssignment->getRole();

        self::assertEquals($role, $userRole);
        self::assertInstanceOf(SectionLimitation::class, $roleLimitation);
        self::assertSame(self::LIMITATION_VALUES, $roleLimitation->limitationValues);
    }
}
