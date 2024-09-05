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
final class MigrateCommandUserGroupCreateTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-group-create.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('__with_identifier__');
        self::assertContentRemoteIdNotExists('__with_id__');
    }

    protected function postCommandAssertions(): void
    {
        $contentService = self::getContentService();
        $content = $contentService->loadContentByRemoteId('__with_identifier__');

        self::assertUserGroup($content);

        $content = $contentService->loadContentByRemoteId('__with_id__');
        self::assertUserGroup($content);
    }
}

class_alias(MigrateCommandUserGroupCreateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandUserGroupCreateTest');
