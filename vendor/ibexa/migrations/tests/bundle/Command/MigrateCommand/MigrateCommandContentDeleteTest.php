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
final class MigrateCommandContentDeleteTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT_REMOTE_ID = 'f5c88a2209584891056f987fd965b0ba';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-delete.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
    }

    protected function postCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists(self::KNOWN_CONTENT_REMOTE_ID);
    }
}

class_alias(MigrateCommandContentDeleteTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentDeleteTest');
