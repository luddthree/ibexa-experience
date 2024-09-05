<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Values\User\User;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandUserUpdateTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-update.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $userService = self::getUserService();
        $user = $userService->loadUserByLogin('admin');

        self::assertInstanceOf(User::class, $user);
        self::assertSame('admin@link.invalid', $user->email);
        self::assertTrue($user->enabled);
        self::assertSame('Administrator', (string) $user->getFieldValue('first_name'));
        self::assertSame('User', (string) $user->getFieldValue('last_name'));
        self::assertSame('$2y$10$FDn9NPwzhq85cLLxfD5Wu.L3SL3Z/LNCvhkltJUV0wcJj7ciJg2oy', $user->passwordHash);

        $user = $userService->loadUserByLogin('anonymous');

        self::assertInstanceOf(User::class, $user);
        self::assertSame('anonymous@link.invalid', $user->email);
        self::assertTrue($user->enabled);
        self::assertSame('Anonymous', (string) $user->getFieldValue('first_name'));
        self::assertSame('User', (string) $user->getFieldValue('last_name'));
        self::assertSame('$2y$10$35gOSQs6JK4u4whyERaeUuVeQBi2TUBIZIfP7HEj7sfz.MxvTuOeC', $user->passwordHash);

        $roleService = self::getRoleService();
        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);
        self::assertCount(0, $roleAssignments);
    }

    protected function postCommandAssertions(): void
    {
        $userService = self::getUserService();
        $user = $userService->loadUserByLogin('admin');

        self::assertInstanceOf(User::class, $user);
        self::assertSame('anonymous@link.invalid', $user->email);
        self::assertFalse($user->enabled);
        self::assertSame('__FIRST_NAME__', (string) $user->getFieldValue('first_name'));
        self::assertSame('__LAST_NAME__', (string) $user->getFieldValue('last_name'));
        self::assertNotSame(
            '$2y$10$FDn9NPwzhq85cLLxfD5Wu.L3SL3Z/LNCvhkltJUV0wcJj7ciJg2oy',
            $user->passwordHash,
            'Password was not changed'
        );

        $user = $userService->loadUserByLogin('anonymous');

        self::assertInstanceOf(User::class, $user);
        self::assertSame('nospam_2@ibexa.co', $user->email);
        self::assertFalse($user->enabled);
        self::assertSame('__FIRST_NAME_2__', (string) $user->getFieldValue('first_name'));
        self::assertSame('__LAST_NAME_2__', (string) $user->getFieldValue('last_name'));
        self::assertNotSame(
            '$2y$10$FDn9NPwzhq85cLLxfD5Wu.L3SL3Z/LNCvhkltJUV0wcJj7ciJg2oy',
            $user->passwordHash,
            'Password was not changed'
        );

        $roleService = self::getRoleService();
        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);
        self::assertCount(1, $roleAssignments);
    }
}

class_alias(MigrateCommandUserUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserUpdateTest');
