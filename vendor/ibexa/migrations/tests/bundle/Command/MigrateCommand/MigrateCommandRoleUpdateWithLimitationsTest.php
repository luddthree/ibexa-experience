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
final class MigrateCommandRoleUpdateWithLimitationsTest extends AbstractMigrateCommand
{
    private const KNOWN_FIXTURE_POLICIES_COUNT = 28;
    private const KNOWN_FIXTURE_ROLE_IDENTIFIER = 'Editor';
    private const KNOWN_COMMAND_RESULTING_LIMITATION_COUNT = 21;

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/role-update-with-limitations.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $role = self::getRoleService()->loadRoleByIdentifier(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);

        self::assertCount(self::KNOWN_FIXTURE_POLICIES_COUNT, $role->policies);
    }

    protected function postCommandAssertions(): void
    {
        $role = self::getRoleService()->loadRoleByIdentifier(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);

        self::assertCount(self::KNOWN_COMMAND_RESULTING_LIMITATION_COUNT, $role->policies);
    }
}

class_alias(MigrateCommandRoleUpdateWithLimitationsTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandRoleUpdateWithLimitationsTest');
