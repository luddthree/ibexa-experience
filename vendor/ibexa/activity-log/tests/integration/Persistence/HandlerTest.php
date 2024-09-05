<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\Persistence;

use DateTime;
use Ibexa\ActivityLog\Persistence\HandlerInterface;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ActionCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LoggedAtCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalAnd;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalNot;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\LogicalOr;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\ObjectNameCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Criterion\UserCriterion;
use Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query;
use Ibexa\Contracts\Core\Collection\MapInterface;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;

final class HandlerTest extends IbexaKernelTestCase
{
    private HandlerInterface $handler;

    protected function setUp(): void
    {
        $this->handler = $this->getIbexaTestCore()->getServiceByClassName(HandlerInterface::class);
    }

    public function testFindByQuery(): void
    {
        $results = $this->handler->findByQuery(new Query());
        self::assertCount(5, $results);
        foreach ($results as $result) {
            self::assertGroupDataShape($result);
        }
    }

    /**
     * @dataProvider provideForQueryWithCriteria
     */
    public function testFindByQueryWithCriteria(Query $query, int $expectedCount): void
    {
        $results = $this->handler->findByQuery($query);
        self::assertCount($expectedCount, $results);
        foreach ($results as $result) {
            self::assertGroupDataShape($result);
        }
    }

    /**
     * @return iterable<string, array{\Ibexa\Contracts\ActivityLog\Values\ActivityLog\Query, int}>
     */
    public static function provideForQueryWithCriteria(): iterable
    {
        yield 'empty query' => [
            new Query(),
            5,
        ];

        yield 'user "0" query' => [
            new Query([new UserCriterion([0])]),
            3,
        ];

        yield 'user "14" & "42" query' => [
            new Query([new UserCriterion([14, 42])]),
            2,
        ];

        yield 'logged at criterion (since 5 minutes ago)' => [
            new Query([new LoggedAtCriterion(new DateTime('2023-10-03 12:45:00'), '>')]),
            5,
        ];

        yield 'logged at criterion (after 5 minutes in the future)' => [
            new Query([new LoggedAtCriterion(new DateTime('2023-10-03 12:55:00'), '>')]),
            0,
        ];

        yield 'logged at criterion (between -5 minutes and +5 minutes)' => [
            new Query([
                new LoggedAtCriterion(new DateTime('2023-10-03 12:45:00'), '>'),
                new LoggedAtCriterion(new DateTime('2023-10-03 12:55:00'), '<'),
            ]),
            5,
        ];

        yield 'object (stdClass) criterion' => [
            new Query([
                new ObjectCriterion('stdClass'),
            ]),
            4,
        ];

        yield 'object (stdClass, ["foo"]) criterion' => [
            new Query([
                new ObjectCriterion('stdClass', ['foo']),
            ]),
            2,
        ];

        yield 'object (stdClass, ["foo", "bar"]) criterion' => [
            new Query([
                new ObjectCriterion('stdClass', ['foo', 'bar']),
            ]),
            4,
        ];

        yield 'object (nonExistent) criterion' => [
            new Query([
                /** @phpstan-ignore-next-line Ignore nonExistent not being a class name */
                new ObjectCriterion('nonExistent'),
            ]),
            0,
        ];

        yield 'object (nonExistent, ["foo", "bar"]) criterion' => [
            new Query([
                /** @phpstan-ignore-next-line Ignore nonExistent not being a class name */
                new ObjectCriterion('nonExistent', ['foo', 'bar']),
            ]),
            0,
        ];

        yield 'action ("foo_action")' => [
            new Query([
                new ActionCriterion(['foo_action']),
            ]),
            3,
        ];

        yield 'logical AND: [object(stdClass)] and [users "14" & "42" query]' => [
            new Query([
                new LogicalAnd([
                    new ObjectCriterion('stdClass'),
                    new UserCriterion([14, 42]),
                ]),
            ]),
            2,
        ];

        yield 'logical OR: [object(stdClass)] and [users "14" & "42" query]' => [
            new Query([
                new LogicalOr([
                    /** @phpstan-ignore-next-line Ignore nonExistent not being a class name */
                    new ObjectCriterion('otherClass'),
                    new UserCriterion([14]),
                ]),
            ]),
            2,
        ];

        yield 'Logical NOT: logged at criterion (after 5 minutes in the future)' => [
            new Query([
                new LogicalNot(
                    new LoggedAtCriterion(new DateTime('+5 minutes'), '>'),
                ),
            ]),
            5,
        ];

        yield 'Logical NOT: object (stdClass, ["foo", "bar"]) criterion' => [
            new Query([
                new LogicalNot(
                    new ObjectCriterion('stdClass', ['foo', 'bar']),
                ),
            ]),
            1,
        ];

        yield 'Actions' => [
            new Query([
                new ActionCriterion(['foo_action', 'bar_action']),
            ]),
            5,
        ];

        yield 'Object Name - ends with "_name"' => [
            new Query([
                new ObjectNameCriterion('_name', 'ENDS_WITH'),
            ]),
            4,
        ];

        yield 'Object Name - starts with "foo"' => [
            new Query([
                new ObjectNameCriterion('foo', 'STARTS_WITH'),
            ]),
            2,
        ];

        yield 'Object Name - contains with "old"' => [
            new Query([
                new ObjectNameCriterion('old', 'CONTAINS'),
            ]),
            2,
        ];
    }

    public function testCountByQuery(): void
    {
        $result = $this->handler->countByQuery(new Query());
        self::assertSame(5, $result);
    }

    public function testSave(): void
    {
        $groupId = $this->handler->initializeGroup(0);
        $this->handler->save(
            $groupId,
            'stdClass',
            'foo',
            'foo_action',
            'foo_name',
            ['foo' => 'bar'],
        );

        $log = $this->handler->find($groupId);
        self::assertNotNull($log);
        self::assertGroupDataShape($log);

        self::assertIsString($log['id']);

        self::assertNull($log['source_id']);
        self::assertNull($log['source']);

        self::assertNull($log['ip_id']);
        self::assertNull($log['ip']);

        self::assertNull($log['ip_id']);
        self::assertInstanceOf(\DateTimeImmutable::class, $log['logged_at']);
        self::assertSame(0, $log['user_id']);

        self::assertCount(1, $log['log_entries']);

        $logEntry = $log['log_entries'][0];
        self::assertIsArray($logEntry['object_class']);
        self::assertSame('stdClass', $logEntry['object_class']['object_class']);
        self::assertSame('foo', $logEntry['object_id']);
        self::assertIsArray($logEntry['action']);
        self::assertSame('foo_action', $logEntry['action']['action']);
        self::assertSame('foo_name', $logEntry['object_name']);
        self::assertInstanceOf(MapInterface::class, $logEntry['data']);
        self::assertSame(['foo' => 'bar'], $logEntry['data']->toArray());
    }

    public function testSaveWithContext(): void
    {
        $groupId = $this->handler->initializeGroup(0, 'foo_source', '0.0.0.0', 'foo_description');
        $this->handler->save(
            $groupId,
            'stdClass',
            'foo',
            'foo_action',
            'foo_name',
            ['foo' => 'bar'],
        );

        $log = $this->handler->find($groupId);
        self::assertNotNull($log);
        self::assertGroupDataShape($log);

        self::assertIsString($log['id']);

        self::assertSame('foo_description', $log['description']);

        self::assertNotNull($log['source_id']);
        self::assertIsArray($log['source']);
        self::assertArrayHasKeys(['id', 'name'], $log['source']);
        self::assertSame($log['source']['name'], 'foo_source');

        self::assertNotNull($log['ip_id']);
        self::assertIsArray($log['ip']);
        self::assertArrayHasKeys(['id', 'ip'], $log['ip']);
        self::assertSame($log['ip']['ip'], '0.0.0.0');

        self::assertInstanceOf(\DateTimeImmutable::class, $log['logged_at']);
        self::assertSame(0, $log['user_id']);

        self::assertCount(1, $log['log_entries']);

        $logEntry = $log['log_entries'][0];
        self::assertIsArray($logEntry['object_class']);
        self::assertSame('stdClass', $logEntry['object_class']['object_class']);
        self::assertSame('foo', $logEntry['object_id']);
        self::assertIsArray($logEntry['action']);
        self::assertSame('foo_action', $logEntry['action']['action']);
        self::assertSame('foo_name', $logEntry['object_name']);
        self::assertInstanceOf(MapInterface::class, $logEntry['data']);
        self::assertSame(['foo' => 'bar'], $logEntry['data']->toArray());
    }

    public function testSaveToExistingGroup(): void
    {
        $groupId = 1;
        $this->handler->save(
            $groupId,
            'stdClass',
            'foo',
            'foo_action',
            'foo_name',
            ['foo' => 'bar'],
        );

        $log = $this->handler->find($groupId);
        self::assertNotNull($log);
        self::assertGroupDataShape($log);

        self::assertIsString($log['id']);
        self::assertNull($log['description']);

        self::assertNull($log['source_id']);
        self::assertNull($log['source']);

        self::assertNull($log['ip_id']);
        self::assertNull($log['ip']);

        self::assertInstanceOf(\DateTimeImmutable::class, $log['logged_at']);
        self::assertSame(0, $log['user_id']);

        self::assertCount(2, $log['log_entries']);

        $logEntry = $log['log_entries'][1];
        self::assertIsArray($logEntry['object_class']);
        self::assertSame('stdClass', $logEntry['object_class']['object_class']);
        self::assertSame('foo', $logEntry['object_id']);
        self::assertIsArray($logEntry['action']);
        self::assertSame('foo_action', $logEntry['action']['action']);
        self::assertSame('foo_name', $logEntry['object_name']);
        self::assertInstanceOf(MapInterface::class, $logEntry['data']);
        self::assertSame(['foo' => 'bar'], $logEntry['data']->toArray());
    }

    /**
     * @depends testCountByQuery
     */
    public function testTruncate(): void
    {
        $count = $this->handler->countByQuery(new Query());
        self::assertSame(5, $count);

        $criteria = [
            new LoggedAtCriterion(new DateTime('2023-10-03 12:45:00'), '>'),
            new LoggedAtCriterion(new DateTime('2023-10-03 12:55:00'), '<'),
        ];

        $this->handler->truncate($criteria);

        $count = $this->handler->countByQuery(new Query());
        self::assertSame(0, $count);
    }

    /**
     * @param array<mixed> $result
     */
    private static function assertGroupDataShape(array $result): void
    {
        $groupEntryShape = [
            'id',
            'description',
            'source_id',
            'ip_id',
            'logged_at',
            'user_id',
            'source',
            'ip',
            'log_entries',
        ];
        self::assertArrayHasKeys($groupEntryShape, $result);

        self::assertIsArray($result['log_entries']);
        foreach ($result['log_entries'] as $logEntry) {
            self::assertIsArray($logEntry);
            self::assertLogEntryShape($logEntry);
        }
    }

    /**
     * @param array<mixed> $logEntry
     */
    private static function assertLogEntryShape(array $logEntry): void
    {
        $logEntryShape = [
            'id',
            'group_id',
            'object_class_id',
            'action_id',
            'object_id',
            'object_name',
            'data',
            'action',
            'object_class',
        ];
        self::assertArrayHasKeys($logEntryShape, $logEntry);
    }

    /**
     * @param array<string> $expectedKeys
     * @param array<mixed> $array
     */
    private static function assertArrayHasKeys(array $expectedKeys, array $array): void
    {
        $actualKeys = array_keys($array);
        self::assertEquals($expectedKeys, $actualKeys);
    }
}
