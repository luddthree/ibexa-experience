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
final class MigrateCommandUserGroupDeleteTest extends AbstractMigrateCommand
{
    private const KNOWN_USER_GROUP_REMOTE_ID = '3c160cca19fb135f83bd02d911f04db2';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-group-delete.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_USER_GROUP_REMOTE_ID);
        self::assertTrue(self::getUserService()->isUserGroup($content));
    }

    protected function postCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists(self::KNOWN_USER_GROUP_REMOTE_ID);
    }
}

class_alias(MigrateCommandUserGroupDeleteTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserGroupDeleteTest');
