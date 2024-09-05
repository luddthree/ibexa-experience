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
final class MigrateCommandRepeatableTest extends AbstractMigrateCommand
{
    protected function getTestContent(): string
    {
        return self::loadFile(__DIR__ . '/migrate-command-fixtures/repeatable.yaml');
    }

    protected function preCommandAssertions(): void
    {
        self::assertContentRemoteIdNotExists('foo 0');
        self::assertContentRemoteIdNotExists('foo 1');
        self::assertContentRemoteIdNotExists('foo 2');
        self::assertContentRemoteIdNotExists('foo 3');
        self::assertContentRemoteIdNotExists('foo 4');
        self::assertContentRemoteIdNotExists('foo 5');
        self::assertContentRemoteIdNotExists('foo 6');
        self::assertContentRemoteIdNotExists('foo 7');
        self::assertContentRemoteIdNotExists('foo 8');
        self::assertContentRemoteIdNotExists('foo 9');
        self::assertContentRemoteIdNotExists('foo 10');
    }

    protected function postCommandAssertions(): void
    {
        self::assertContentRemoteIdExists('foo 0');
        self::assertContentRemoteIdExists('foo 1');
        self::assertContentRemoteIdExists('foo 2');
        self::assertContentRemoteIdExists('foo 3');
        self::assertContentRemoteIdExists('foo 4');
        self::assertContentRemoteIdExists('foo 5');
        self::assertContentRemoteIdExists('foo 6');
        self::assertContentRemoteIdExists('foo 7');
        self::assertContentRemoteIdExists('foo 8');
        self::assertContentRemoteIdExists('foo 9');
        self::assertContentRemoteIdNotExists('foo 10');
    }
}
