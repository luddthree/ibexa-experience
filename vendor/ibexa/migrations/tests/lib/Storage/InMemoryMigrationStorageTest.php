<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Storage;

use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\Storage\InMemoryMigrationStorage;
use PHPUnit\Framework\TestCase;

final class InMemoryMigrationStorageTest extends TestCase
{
    public function testLoadOne(): void
    {
        $migration = new Migration('foo', 'foo');

        $storage = new InMemoryMigrationStorage([
            'foo' => $migration,
        ]);

        $this->assertSame($migration, $storage->loadOne('foo'));
        $this->assertNull($storage->loadOne('non-existing'));
    }

    public function testLoadAll(): void
    {
        $migrations = [
            'foo' => new Migration('foo', 'foo'),
            'bar' => new Migration('bar', 'bar'),
            'baz' => new Migration('baz', 'baz'),
        ];

        $storage = new InMemoryMigrationStorage($migrations);

        $this->assertEquals($migrations, $storage->loadAll());
    }

    /**
     * @depends Ibexa\Tests\Migration\Storage\InMemoryMigrationStorageTest::testLoadOne
     */
    public function testStore(): void
    {
        $migration = new Migration('test', 'foo');

        $storage = new InMemoryMigrationStorage();
        $storage->store($migration);

        $this->assertSame($migration, $storage->loadOne('test'));
    }
}

class_alias(InMemoryMigrationStorageTest::class, 'Ibexa\Platform\Tests\Migration\Storage\InMemoryMigrationStorageTest');
