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
final class MigrateCommandContentTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/1605771536_4528_content.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('foo_landing_page');
    }

    protected function postCommandAssertions(): void
    {
        self::assertContentRemoteIdExists('foo_landing_page');
    }
}

class_alias(MigrateCommandContentTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandContentTest');
