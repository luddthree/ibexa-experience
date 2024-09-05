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
final class MigrateCommandContentUpdateTest extends AbstractMigrateCommand
{
    private const KNOWN_CONTENT_REMOTE_ID = '8a9c9c761004866fb458d89910f52bee';

    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/content-update.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
        $field = $content->getField('name');
        self::assertNotNull($field);
        self::assertSame('Home', (string) $field->value);
    }

    protected function postCommandAssertions(): void
    {
        $content = self::assertContentRemoteIdExists(self::KNOWN_CONTENT_REMOTE_ID);
        $field = $content->getField('name');
        self::assertNotNull($field);
        self::assertSame('Partners', (string) $field->value);
    }
}

class_alias(MigrateCommandContentUpdateTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentUpdateTest');
