<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\Metadata\Storage;

use DateTimeImmutable;
use Ibexa\Migration\Metadata\ExecutedMigration;
use Ibexa\Migration\Metadata\ExecutionResult;
use Ibexa\Migration\Metadata\Storage\InMemoryMetadataStorage;
use PHPUnit\Framework\TestCase;

final class InMemoryMetadataStorageTest extends TestCase
{
    public function testGetExecutedMigrations(): void
    {
        $expectedMigrations = [
            new ExecutedMigration('foo'),
            new ExecutedMigration('bar'),
            new ExecutedMigration('baz'),
        ];

        $storage = new InMemoryMetadataStorage($expectedMigrations);

        $this->assertEquals(
            $expectedMigrations,
            $storage->getExecutedMigrations()->getItems()
        );
    }

    /**
     * @depends Ibexa\Tests\Migration\Metadata\Storage\InMemoryMetadataStorageTest::testGetExecutedMigrations
     */
    public function testReset(): void
    {
        $expectedMigrations = [
            new ExecutedMigration('foo'),
            new ExecutedMigration('bar'),
            new ExecutedMigration('baz'),
        ];

        $storage = new InMemoryMetadataStorage($expectedMigrations);
        $storage->reset();

        $this->assertEquals(0, $storage->getExecutedMigrations()->count());
        $this->assertEmpty($storage->getExecutedMigrations()->getItems());
    }

    /**
     * @depends Ibexa\Tests\Migration\Metadata\Storage\InMemoryMetadataStorageTest::testGetExecutedMigrations
     */
    public function testComplete(): void
    {
        $datatime = new DateTimeImmutable();

        $executionResult = new ExecutionResult('foo');
        $executionResult->setExecutedAt($datatime);
        $executionResult->setTime(30.0);

        $storage = new InMemoryMetadataStorage();
        $storage->complete($executionResult);

        $this->assertEquals(
            new ExecutedMigration('foo', $datatime, 30.0),
            $storage->getExecutedMigrations()->getMigration('foo')
        );
    }
}

class_alias(InMemoryMetadataStorageTest::class, 'Ibexa\Platform\Tests\Migration\Metadata\Storage\InMemoryMetadataStorageTest');
