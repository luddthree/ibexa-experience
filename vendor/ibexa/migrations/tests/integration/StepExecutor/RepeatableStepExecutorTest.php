<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Migration\StepExecutor;

use Ibexa\Migration\StepExecutor\RepeatableStepExecutor;
use Ibexa\Migration\StepExecutor\SQLExecuteStepExecutor;
use Ibexa\Migration\StepExecutor\StepExecutorManager;
use Ibexa\Migration\ValueObject\Sql\Query;
use Ibexa\Migration\ValueObject\Step\RepeatableStep;
use Ibexa\Migration\ValueObject\Step\SQLExecuteStep;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\Tests\Migration\StepExecutor\AbstractInitializedStepExecutorTest;

/**
 * @covers \Ibexa\Migration\StepExecutor\RepeatableStepExecutor
 */
final class RepeatableStepExecutorTest extends AbstractInitializedStepExecutorTest
{
    private RepeatableStepExecutor $executor;

    protected function setUp(): void
    {
        parent::setUp();
        $executorManager = new StepExecutorManager([new SQLExecuteStepExecutor(self::getDoctrineConnection())]);
        $this->executor = new RepeatableStepExecutor($executorManager);
        $this->configureExecutor($this->executor);
    }

    public function testCanHandle(): void
    {
        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new RepeatableStep([]);
        self::assertTrue($this->executor->canHandle($step));
    }

    /**
     * @dataProvider provideForHandling
     *
     * @param mixed $value
     */
    public function testHandling(RepeatableStep $step, $value): void
    {
        self::assertEquals(
            0,
            $this->getDoctrineConnection()->executeQuery('SELECT COUNT(name) FROM repeatable')->fetchOne()
        );

        $this->executor->handle($step);

        self::assertEquals(
            $value,
            $this->getDoctrineConnection()->executeQuery('SELECT COUNT(name) FROM repeatable')->fetchOne()
        );
    }

    /**
     * @return iterable<string, array{
     *     \Ibexa\Migration\ValueObject\Step\RepeatableStep,
     *     mixed,
     * }>
     */
    public function provideForHandling(): iterable
    {
        $sqlStep = new SQLExecuteStep([
            new Query(
                'sqlite',
                'INSERT INTO repeatable(name) VALUES ("Test")'
            ),
            new Query(
                'postgresql',
                "INSERT INTO repeatable(name) VALUES ('Test')"
            ),
            new Query(
                'mysql',
                'INSERT INTO repeatable(name) VALUES ("Test")'
            ),
        ]);
        yield 'repeatable-example' => [
            new RepeatableStep([$sqlStep, $sqlStep, $sqlStep, $sqlStep, $sqlStep]),
            5,
        ];
    }
}
