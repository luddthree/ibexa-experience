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
final class MigrateCommandUserGroupCreateMinimalTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/user-group-create-minimal.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('__with_identifier__');
    }

    protected function postCommandAssertions(): void
    {
        $contentService = self::getContentService();
        $content = $contentService->loadContentByRemoteId('__with_identifier__');

        self::assertUserGroup($content);
    }
}
