<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ActivityLog\Persistence\ActivityLog;

use Doctrine\Common\Collections\Expr\Comparison;
use Ibexa\ActivityLog\Persistence\ActivityLog\GatewayInterface;
use Ibexa\Contracts\Test\Core\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ActivityLog\Persistence\ActivityLog\DoctrineDatabase
 */
final class DoctrineDatabaseTest extends IbexaKernelTestCase
{
    private GatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = $this->getIbexaTestCore()->getServiceByClassName(GatewayInterface::class);
    }

    /**
     * @dataProvider provideQueries
     */
    public function testSavingAndMatching(Comparison $comparison, int $expectedCount): void
    {
        $id = $this->gateway->save(
            1,
            1,
            1, // Fixture
            '123',
            'foo_name',
            ['foo' => 'bar'],
        );

        $log = $this->gateway->findById($id);

        self::assertIsArray($log);
        self::assertIsString($log['id']);
        self::assertSame('1', $log['group_id']);
        self::assertSame(1, $log['object_class_id']);
        self::assertSame('123', $log['object_id']);
        self::assertSame(1, $log['action_id']);
        self::assertSame('foo_name', $log['object_name']);
        self::assertSame(['foo' => 'bar'], $log['data']);

        $collection = $this->gateway->findBy($comparison);
        self::assertCount($expectedCount, $collection);
    }

    /**
     * @return iterable<array{\Doctrine\Common\Collections\Expr\Comparison, int}>
     */
    public static function provideQueries(): iterable
    {
        yield [
            new Comparison('object_class.object_class', '=', 'nonExistent'),
            0,
        ];

        yield [
            new Comparison('object_class.object_class', '=', 'stdClass'),
            5,
        ];

        yield [
            new Comparison('object_id', 'IN', [123]),
            1,
        ];

        yield [
            new Comparison('object_id', 'IN', ['foo']),
            3,
        ];
    }
}
