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
final class MigrateCommandUserGroupCreateWithReferencesTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-group-create-with-references.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('__foo__');
    }

    protected function postCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists('__foo__');
        $firstUserGroup = self::assertUserGroup($content);

        $content = self::assertContentRemoteIdExists('__bar__');
        $secondUserGroup = self::assertUserGroup($content);

        self::assertSame($firstUserGroup->id, $secondUserGroup->parentId);
    }
}

class_alias(MigrateCommandUserGroupCreateWithReferencesTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserGroupCreateWithReferencesTest');
