<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Migration\Reference\CollectorInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandUserCreateWithReferencesTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-create-with-references.yaml');
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

        $collector = self::getServiceByClassName(CollectorInterface::class);
        self::assertFalse($collector->getCollection()->has('ref__user_id'));
    }

    protected function postCommandAssertions(): void
    {
        $userService = self::getUserService();
        $roleService = self::getRoleService();
        $user = $userService->loadUserByLogin('__foo__');

        self::assertInstanceOf(User::class, $user);

        $roleAssignments = $roleService->getRoleAssignmentsForUser($user);

        self::assertCount(1, $roleAssignments);

        $collector = self::getServiceByClassName(CollectorInterface::class);
        self::assertTrue($collector->getCollection()->has('ref__user_id'));
    }
}
