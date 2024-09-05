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
final class MigrateCommandSQLExecuteTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/sql-execute.yaml');
    }

    protected function preCommandAssertions(): void
    {
        $connection = self::getDoctrineConnection();
        $result = $connection->executeQuery('SELECT COUNT(test_value) FROM test_table');
        self::assertEquals(0, $result->fetchOne());
    }

    protected function postCommandAssertions(): void
    {
        $connection = self::getDoctrineConnection();
        $result = $connection->executeQuery('SELECT COUNT(test_value) FROM test_table');
        self::assertEquals(1, $result->fetchOne());
    }
}

class_alias(MigrateCommandSQLExecuteTest::class, 'Ibexa\Platform\Tests\Bundle\Migration\Command\MigrateCommand\MigrateCommandSQLExecuteTest');
