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
final class MigrateCommandRoleCreateWithReferencesCreateTest extends AbstractMigrateCommandRoleCreateTest
{
    private const IDENTIFIER = 'foo';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/role-create-with-references.yaml');
    }

    protected function postCommandAssertions(): void
    {
        $roleService = self::getRoleService();
        $content = self::assertContentRemoteIdExists('__bar__role__');
        $role = $roleService->loadRoleByIdentifier(self::IDENTIFIER);

        self::assertSame(self::IDENTIFIER, $role->identifier);

        self::assertCount(1, $role->getPolicies());

        $userGroup = self::assertUserGroup($content);

        self::assertUserGroupHasRoles($userGroup, [self::IDENTIFIER]);
    }
}

class_alias(MigrateCommandRoleCreateWithReferencesCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandRoleCreateWithReferencesCreateTest');
