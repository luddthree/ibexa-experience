<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Values\User\User;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandUserCreateTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-create.yaml');
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
        $user = $userService->loadUserByLogin('__foo__');

        self::assertInstanceOf(User::class, $user);

        $user = $userService->loadUserByLogin('__foo__admin__');
        self::assertInstanceOf(User::class, $user);
    }
}

class_alias(MigrateCommandUserCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserCreateTest');
