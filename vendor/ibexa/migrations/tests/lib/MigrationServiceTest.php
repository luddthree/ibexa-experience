<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration;

use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\Repository\Values\User\UserReference;
use Ibexa\Contracts\Migration\Exception\MigrationAlreadyExecutedException;
use Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Contracts\Migration\MigrationExecutor;
use Ibexa\Contracts\Migration\MigrationStorage;
use Ibexa\Migration\Metadata\ExecutedMigration;
use Ibexa\Migration\Metadata\ExecutedMigrationsList;
use Ibexa\Migration\Metadata\ExecutionResult;
use Ibexa\Migration\MigrationService;
use Ibexa\Migration\Repository\Migration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * @covers \Ibexa\Migration\MigrationService
 */
final class MigrationServiceTest extends TestCase
{
    private MigrationService $service;

    /** @var \Ibexa\Contracts\Migration\Metadata\Storage\MetadataStorage|\PHPUnit\Framework\MockObject\MockObject */
    private MetadataStorage $metadataStorage;

    /** @var \Ibexa\Contracts\Migration\MigrationStorage|\PHPUnit\Framework\MockObject\MockObject */
    private MigrationStorage $storage;

    /** @var \Ibexa\Contracts\Migration\MigrationExecutor|\PHPUnit\Framework\MockObject\MockObject */
    private MigrationExecutor $executor;

    /** @var \Ibexa\Contracts\Core\Repository\UserService|\PHPUnit\Framework\MockObject\MockObject */
    private UserService $userService;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver|\PHPUnit\Framework\MockObject\MockObject */
    private PermissionResolver $permissionResolver;

    protected function setUp(): void
    {
        $this->service = new MigrationService(
            $this->storage = $this->createMock(MigrationStorage::class),
            $this->metadataStorage = $this->createMock(MetadataStorage::class),
            $this->executor = $this->createMock(MigrationExecutor::class),
            $this->userService = $this->createMock(UserService::class),
            $this->permissionResolver = $this->createMock(PermissionResolver::class),
            new Stopwatch(),
            'default_login',
        );
    }

    public function testFindOneByName(): void
    {
        $migration = new Migration('test_name', '');
        $this->storage->expects(self::once())
            ->method('loadOne')
            ->with('test_name')
            ->willReturn($migration);

        $result = $this->service->findOneByName('test_name');

        self::assertSame($migration, $result);
    }

    public function testExecuteAllCallsExecutorWithEachMigration(): void
    {
        $migration1 = new Migration('1', '');
        $migration2 = new Migration('2', '');

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([]));

        $this->storage->method('loadAll')
            ->willReturn([$migration1, $migration2]);

        $this->executor->expects(self::exactly(2))
            ->method('execute')
            ->withConsecutive([$migration1], [$migration2]);

        $this->service->executeAll();
    }

    public function testThrowsExceptionWhenExecutedAlreadyExecutedMigration(): void
    {
        $migration1 = new Migration('1', '');
        $executedMigration1 = new ExecutedMigration('1');

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([$executedMigration1]));

        $this->executor->expects(self::never())
            ->method('execute')
            ->withConsecutive([$migration1]);

        self::expectException(MigrationAlreadyExecutedException::class);
        self::expectExceptionMessage('"1" migration is already executed.');
        $this->service->executeOne($migration1);
    }

    public function testExecutesASingleNewMigration(): void
    {
        $migration1 = new Migration('1', '');
        $executedMigration2 = new ExecutedMigration('2');

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([$executedMigration2]));

        $this->executor->expects(self::once())
            ->method('execute')
            ->withConsecutive([$migration1]);

        $this->service->executeOne($migration1);
    }

    public function testReturnsIfMigrationWasAlreadyExecuted(): void
    {
        $migration1 = new Migration('1', '');
        $migration2 = new Migration('2', '');
        $executedMigration1 = new ExecutedMigration('1');

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([$executedMigration1]));

        self::assertTrue($this->service->isMigrationExecuted($migration1));
        self::assertFalse($this->service->isMigrationExecuted($migration2));
    }

    public function testExecuteAllSwitchesBetweenUserPermissionsAndRevertsBackWhenFinished(): void
    {
        $firstUser = $this->createMock(UserReference::class);
        $this->permissionResolver->method('getCurrentUserReference')
            ->willReturn($firstUser);

        $user = $this->createMock(User::class);
        $this->userService->method('loadUserByLogin')
            ->willReturn($user);

        $this->permissionResolver->expects(self::exactly(2))
            ->method('setCurrentUserReference')
            ->withConsecutive([$user], [$firstUser])
        ;

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([]));

        $this->service->executeAll();
    }

    public function testExecuteOneCallsExecutorExactlyOnce(): void
    {
        $migration1 = new Migration('1', '');

        $this->executor->expects(self::once())
            ->method('execute')
            ->with($migration1);

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([]));

        $this->metadataStorage->expects(self::once())
            ->method('complete')
            ->with(self::logicalAnd(
                self::isInstanceOf(ExecutionResult::class),
                self::callback(static function (ExecutionResult $result): bool {
                    return $result->getName() === '1';
                }),
            ));

        $this->service->executeOne($migration1);
    }

    public function testExecuteOneSwitchesBetweenUserPermissionsAndRevertsBackWhenFinished(): void
    {
        $firstUser = $this->createMock(UserReference::class);
        $this->permissionResolver->method('getCurrentUserReference')
            ->willReturn($firstUser);

        $user = $this->createMock(User::class);
        $this->userService->method('loadUserByLogin')
            ->willReturn($user);

        $this->permissionResolver->expects(self::exactly(2))
            ->method('setCurrentUserReference')
            ->withConsecutive([$user], [$firstUser])
        ;

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([]));

        $this->service->executeOne(new Migration('', ''));
    }

    public function testListMigrations(): void
    {
        $migration1 = new Migration('1_test_name', '');
        $migration2 = new Migration('2_test_name', '');
        $this->storage
            ->method('loadAll')
            ->willReturn([$migration2, $migration1]);

        $result = $this->service->listMigrations();

        self::assertSame([
            $migration1,
            $migration2,
        ], $result);
    }

    public function testAdd(): void
    {
        $migration = new Migration('', '');

        $this->storage
            ->expects(self::once())
            ->method('store')
            ->with($migration);

        $this->service->add($migration);
    }

    public function testExecuteAllSkipsAlreadyExecuted(): void
    {
        $migration1 = new Migration('foo', '');
        $migration2 = new Migration('bar', '');

        $this->storage
            ->method('loadAll')
            ->willReturn([
                $migration1,
                $migration2,
            ]);

        $this->executor
            ->expects(self::once())
            ->method('execute')
            ->with($migration2);

        $this->metadataStorage
            ->method('getExecutedMigrations')
            ->willReturn(new ExecutedMigrationsList([
                new ExecutedMigration('foo'),
            ]));

        $this->metadataStorage
            ->expects(self::once())
            ->method('complete')
            ->with(self::logicalAnd(
                self::isInstanceOf(ExecutionResult::class),
                self::callback(static function (ExecutionResult $result): bool {
                    return $result->getName() === 'bar';
                })
            ));

        $this->service->executeAll();
    }
}
