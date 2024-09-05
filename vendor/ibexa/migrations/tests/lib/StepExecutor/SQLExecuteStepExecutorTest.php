<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Migration\StepExecutor;

use Ibexa\Migration\StepExecutor\SQLExecuteStepExecutor;
use Ibexa\Migration\ValueObject\Sql\Query;
use Ibexa\Migration\ValueObject\Step\SQLExecuteStep;

/**
 * @covers \Ibexa\Migration\StepExecutor\SQLExecuteStepExecutor
 */
final class SQLExecuteStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    public function testHandle(): void
    {
        $connection = self::getDoctrineConnection();
        $result = $connection->executeQuery('SELECT COUNT(test_value) FROM test_table');
        self::assertEquals(0, $result->fetchOne());

        $stepExecutor = new SQLExecuteStepExecutor(
            $connection
        );

        $step = new SQLExecuteStep([
            new Query(
                'mysql',
                'INSERT INTO test_table (test_value) VALUES ("foo");'
            ),
            new Query(
                'sqlite',
                'INSERT INTO test_table (test_value) VALUES ("foo");'
            ),
            new Query(
                'postgresql',
                "INSERT INTO test_table (test_value) VALUES ('foo');"
            ),
        ]);

        $stepExecutor->handle($step);

        $result = $connection->executeQuery('SELECT COUNT(test_value) FROM test_table');
        self::assertEquals(1, $result->fetchOne());
    }
}

class_alias(SQLExecuteStepExecutorTest::class, 'Ibexa\Platform\Tests\Migration\StepExecutor\SQLExecuteStepExecutorTest');
