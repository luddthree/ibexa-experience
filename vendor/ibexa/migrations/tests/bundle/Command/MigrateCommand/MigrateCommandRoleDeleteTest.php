<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandRoleDeleteTest extends AbstractMigrateCommand
{
    private const KNOWN_FIXTURE_ROLE_IDENTIFIER = 'Editor';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/role-delete.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::getRoleService()->loadRoleByIdentifier(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);
    }

    protected function postCommandAssertions(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Could not find 'Persistence User Role' with identifier 'ID: Editor, Status: 0'");
        self::getRoleService()->loadRoleByIdentifier(self::KNOWN_FIXTURE_ROLE_IDENTIFIER);
    }
}

class_alias(MigrateCommandRoleDeleteTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandRoleDeleteTest');
