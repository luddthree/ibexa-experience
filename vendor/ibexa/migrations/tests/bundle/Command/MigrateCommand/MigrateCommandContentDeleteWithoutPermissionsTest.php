<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Command\MigrateCommand;

use Ibexa\Contracts\Core\Repository\Repository;

/**
 * @covers \Ibexa\Bundle\Migration\Command\MigrateCommand
 */
final class MigrateCommandContentDeleteWithoutPermissionsTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT_REMOTE_ID = 'faaeb9be3bd98ed09f606fc16d144eca';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-delete-without-permissions.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $repository = self::getServiceByClassName(Repository::class);
        $repository->sudo(static function (Repository $repository): void {
            $contentService = $repository->getContentService();
            $contentService->loadContentByRemoteId(self::KNOWN_CONTENT_REMOTE_ID);
        });

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Ibexa\Migration\ValueObject\Step\ContentDeleteStep failed execution. No content found matching filters.');
    }

    protected function postCommandAssertions(): void
    {
    }
}

class_alias(MigrateCommandContentDeleteWithoutPermissionsTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentDeleteWithoutPermissionsTest');
