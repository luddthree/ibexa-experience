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
final class MigrateCommandContentCreateWithReferencesTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT_REMOTE_ID = 'foo';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-create-with-references.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists(self::KNOWN_CONTENT_REMOTE_ID);
    }

    protected function postCommandAssertions(): void
    {
        self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
    }
}

class_alias(MigrateCommandContentCreateWithReferencesTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentCreateWithReferencesTest');
