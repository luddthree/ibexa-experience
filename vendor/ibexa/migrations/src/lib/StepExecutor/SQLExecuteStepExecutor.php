<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Migration\StepExecutor;

use Doctrine\DBAL\Connection;
use Exception;
use Ibexa\Migration\Log\LoggerAwareTrait;
use Ibexa\Migration\ValueObject\Sql\Query;
use Ibexa\Migration\ValueObject\Step\SQLExecuteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class SQLExecuteStepExecutor implements StepExecutorInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Doctrine\DBAL\Connection */
    private $connection;

    public function __construct(Connection $connection, ?LoggerInterface $logger = null)
    {
        $this->connection = $connection;
        $this->logger = $logger ?? new NullLogger();
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof SQLExecuteStep;
    }

    /**
     * @param \Ibexa\Migration\ValueObject\Step\SQLExecuteStep $step
     */
    public function handle(StepInterface $step): void
    {
        $dbType = $this->connection->getDatabasePlatform()->getName();

        $filteredQueries = array_filter($step->queries, static function (Query $query) use ($dbType): bool {
            return $query->driver === $dbType;
        });

        if (count($filteredQueries) === 0) {
            throw new \RuntimeException(
                sprintf('No queries that matched use current database driver (%s) provided', $dbType)
            );
        }

        $this->connection->beginTransaction();

        try {
            foreach ($filteredQueries as $query) {
                $this->connection->executeStatement($query->sql);

                $this->getLogger()->notice(sprintf(
                    "Executing SQL using %s platform: \n%s",
                    $dbType,
                    $query->sql,
                ));
            }

            $this->connection->commit();
        } catch (Exception $e) {
            $this->connection->rollback();
            throw $e;
        }
    }
}

class_alias(SQLExecuteStepExecutor::class, 'Ibexa\Platform\Migration\StepExecutor\SQLExecuteStepExecutor');
