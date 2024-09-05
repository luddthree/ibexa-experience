<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Migration\Exception\InvalidMigrationException;
use Ibexa\Contracts\Migration\Exception\UnhandledMigrationException;
use Ibexa\Migration\MigrationExecutor;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\StepExecutor\StepExecutorManagerInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

final class MigrationExecutorTest extends TestCase
{
    private MigrationExecutor $migrationExecutor;

    /** @var \Symfony\Component\Serializer\SerializerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $serializer;

    /** @var \Ibexa\Migration\StepExecutor\StepExecutorManagerInterface&\PHPUnit\Framework\MockObject\MockObject */
    private StepExecutorManagerInterface $stepExecutorManager;

    protected function setUp(): void
    {
        $this->serializer = $this->createMock(SerializerInterface::class);
        $this->stepExecutorManager = $this->createMock(StepExecutorManagerInterface::class);

        $this->migrationExecutor = new MigrationExecutor(
            $this->serializer,
            $this->stepExecutorManager,
            $this->createMock(TransactionHandler::class),
        );
    }

    public function testInvalidMigrationThrowsException(): void
    {
        $this->configureSerializerToReturnOneMigrationStep();
        $this->configureStepExecutorManagerToThrowException(new \InvalidArgumentException('foo_message'));

        $this->expectException(InvalidMigrationException::class);
        $this->expectExceptionMessage('Executing migration "<MIGRATION_NAME>" failed. Exception: foo_message');
        $this->migrationExecutor->execute(new Migration('<MIGRATION_NAME>', '<MIGRATION_CONTENT>'));
    }

    public function testUnhandledMigrationThrowsException(): void
    {
        $this->configureSerializerToReturnOneMigrationStep();
        $e = $this->createMock(\Throwable::class);
        $this->configureStepExecutorManagerToThrowException($e);

        $this->expectException(UnhandledMigrationException::class);
        $this->expectExceptionMessage('Executing migration "<MIGRATION_NAME>" failed. Exception: ');
        $this->migrationExecutor->execute(new Migration('<MIGRATION_NAME>', '<MIGRATION_CONTENT>'));
    }

    private function configureStepExecutorManagerToThrowException(\Throwable $e): void
    {
        $this->stepExecutorManager
            ->expects(self::atLeastOnce())
            ->method('handle')
            ->willThrowException($e);
    }

    private function configureSerializerToReturnOneMigrationStep(): void
    {
        $this->serializer
            ->expects(self::atLeastOnce())
            ->method('deserialize')
            ->willReturn([
                $this->createMock(StepInterface::class),
            ]);
    }
}
