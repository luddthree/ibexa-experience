<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandUserIncompleteUpdateTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-update-incomplete.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $userService = self::getUserService();
        $user = $userService->loadUserByLogin('admin');

        self::assertSame('admin@link.invalid', $user->email);
        self::assertTrue($user->enabled);
        self::assertSame('Administrator', (string) $user->getFieldValue('first_name'));
        self::assertSame('User', (string) $user->getFieldValue('last_name'));
        self::assertSame('$2y$10$FDn9NPwzhq85cLLxfD5Wu.L3SL3Z/LNCvhkltJUV0wcJj7ciJg2oy', $user->passwordHash);
    }

    protected function postCommandAssertions(): void
    {
        $userService = self::getUserService();
        $user = $userService->loadUserByLogin('admin');

        self::assertSame('admin@link.invalid', $user->email);
        self::assertTrue($user->enabled);
        self::assertSame('Administrator', (string) $user->getFieldValue('first_name'));
        self::assertSame('User', (string) $user->getFieldValue('last_name'));
        self::assertSame('$2y$10$FDn9NPwzhq85cLLxfD5Wu.L3SL3Z/LNCvhkltJUV0wcJj7ciJg2oy', $user->passwordHash);
    }
}

class_alias(MigrateCommandUserIncompleteUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserIncompleteUpdateTest');
