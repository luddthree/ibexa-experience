<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use function iterator_to_array;
use Traversable;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
class MigrateCommandUserCreateWithActionsTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-create-with-actions.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $userService = self::getUserService();
        $found = true;

        try {
            $userService->loadUserByLogin('__foo__');
        } catch (NotFoundException $e) {
            $found = false;
        }

        self::assertFalse($found);
    }

    protected function postCommandAssertions(): void
    {
        $userService = self::getUserService();
        $roleService = self::getRoleService();

        $user = $userService->loadUserByLogin('__foo__');
        self::assertInstanceOf(User::class, $user);

        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);
        if ($roleAssignments instanceof Traversable) {
            $roleAssignments = iterator_to_array($roleAssignments);
        }
        self::assertCount(1, $roleAssignments);
        self::assertNotNull($roleAssignments[0]->limitation);

        $user = $userService->loadUserByLogin('__foo__admin__');
        self::assertInstanceOf(User::class, $user);

        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);
        if ($roleAssignments instanceof Traversable) {
            $roleAssignments = iterator_to_array($roleAssignments);
        }
        self::assertCount(1, $roleAssignments);
        self::assertNotNull($roleAssignments[0]->limitation);
    }
}

class_alias(MigrateCommandUserCreateWithActionsTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserCreateWithActionsTest');
