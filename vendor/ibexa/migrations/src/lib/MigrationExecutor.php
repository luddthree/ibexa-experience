<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration;

use Ibexa\Contracts\Core\Persistence\TransactionHandler;
use Ibexa\Contracts\Migration\Exception\InvalidMigrationException;
use Ibexa\Contracts\Migration\Exception\UnhandledMigrationException;
use Ibexa\Contracts\Migration\MigrationExecutor as MigrationExecutorInterface;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\Repository\Migration;
use Ibexa\Migration\StepExecutor\StepExecutorManagerInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

final class MigrationExecutor implements MigrationExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private SerializerInterface $serializer;

    private StepExecutorManagerInterface $stepExecutorManager;

    private TransactionHandler $transactionHandler;

    public function __construct(
        SerializerInterface $serializer,
        StepExecutorManagerInterface $stepExecutorManager,
        TransactionHandler $transactionHandler,
        ?LoggerInterface $logger = null
    ) {
        $this->serializer = $serializer;
        $this->stepExecutorManager = $stepExecutorManager;
        $this->transactionHandler = $transactionHandler;
        $this->logger = $logger ?? new NullLogger();
    }

    public function execute(Migration $migration): void
    {
        $steps = $this->serializer->deserialize(
            $migration->getContent(),
            StepInterface::class . '[]',
            'yaml'
        );

        $this->transactionHandler->beginTransaction();

        try {
            foreach ($steps as $step) {
                $this->stepExecutorManager->handle($step);
            }
            $this->transactionHandler->commit();
        } catch (InvalidArgumentException $e) {
            $this->transactionHandler->rollback();
            throw new InvalidMigrationException($migration, $e);
        } catch (Throwable $e) {
            $this->transactionHandler->rollback();
            throw new UnhandledMigrationException($migration, $e);
        }
    }
}

class_alias(MigrationExecutor::class, 'Ibexa\Platform\Migration\MigrationExecutor');
