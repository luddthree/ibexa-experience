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
final class MigrateCommandRoleUpdateWithReferencesCreateTest extends AbstractMigrateCommandRoleCreateTest
{
    private const IDENTIFIER = 'Editor';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/role-update-with-references.yaml');
    }

    protected function postCommandAssertions(): void
    {
        $roleService = self::getRoleService();
        $content = self::assertContentRemoteIdExists('__foo__role__');
        $role = $roleService->loadRoleByIdentifier(self::IDENTIFIER);

        self::assertSame(self::IDENTIFIER, $role->identifier);

        $userGroup = self::assertUserGroup($content);

        self::assertUserGroupHasRoles($userGroup, [self::IDENTIFIER]);
    }
}
