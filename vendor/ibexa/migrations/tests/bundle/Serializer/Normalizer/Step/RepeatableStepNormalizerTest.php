<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration\Serializer\Normalizer\Step;

use Ibexa\Migration\ValueObject\Sql\Query;
use Ibexa\Migration\ValueObject\Step\RepeatableStep;
use Ibexa\Migration\ValueObject\Step\ServiceCallExecuteStep;
use Ibexa\Migration\ValueObject\Step\SQLExecuteStep;
use Ibexa\Tests\Bundle\Migration\IbexaKernelTestCase;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @covers \Ibexa\Bundle\Migration\Serializer\Normalizer\Step\RepeatableStepNormalizer
 */
final class RepeatableStepNormalizerTest extends IbexaKernelTestCase
{
    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->serializer = self::getMigrationSerializer();
    }

    /**
     * @dataProvider provideFailingData
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testDenormalizeFailure(
        string $data,
        string $exceptionClass,
        ?string $exceptionMessage = null
    ): void {
        $this->expectException($exceptionClass);
        if ($exceptionMessage !== null) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        $this->serializer->deserialize($data, RepeatableStep::class, 'yaml');
    }

    /**
     * @param array<\Ibexa\Migration\ValueObject\Step\StepInterface> $expectedResult
     *
     * @dataProvider provideSuccessfulData
     */
    public function testDenormalizeSuccess(string $data, array $expectedResult): void
    {
        $actualResult = $this->serializer->deserialize($data, RepeatableStep::class, 'yaml');
        self::assertInstanceOf(RepeatableStep::class, $actualResult);
        $steps = $actualResult->getSteps();
        self::assertInstanceOf(\Generator::class, $steps);
        self::assertEquals($expectedResult, iterator_to_array($steps, false));
    }

    /**
     * @return iterable<array{string, array<\Ibexa\Migration\ValueObject\Step\StepInterface>}>
     */
    public function provideSuccessfulData(): iterable
    {
        $sqlStep = new SQLExecuteStep([
            new Query(
                'mysql',
                'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
            ),
            new Query(
                'sqlite',
                'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'
            ),
        ]);

        $serviceCallStep = new ServiceCallExecuteStep(
            '__foo_service__',
            [],
            '__foo_method__',
        );

        yield [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 1
            steps:
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
                        -
                            driver: sqlite
                            sql: 'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'

            YAML,
            [$sqlStep],
        ];

        yield [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 5
            steps:
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
                        -
                            driver: sqlite
                            sql: 'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'

            YAML,
            [$sqlStep, $sqlStep, $sqlStep, $sqlStep, $sqlStep],
        ];

        yield [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 1
            steps:
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
                        -
                            driver: sqlite
                            sql: 'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'
                -
                    type: service_call
                    mode: execute
                    service: __foo_service__
                    method: __foo_method__

            YAML,
            [$sqlStep, $serviceCallStep],
        ];

        yield [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 2
            steps:
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration_mysql` (`name` varchar(255) NOT NULL);'
                        -
                            driver: sqlite
                            sql: 'CREATE TABLE `test_migration_sqlite` (`name` varchar(255) NOT NULL);'
                -
                    type: service_call
                    mode: execute
                    service: __foo_service__
                    method: __foo_method__

            YAML,
            [$sqlStep, $serviceCallStep, $sqlStep, $serviceCallStep],
        ];

        yield [
            <<<YAML
            type: repeatable
            mode: create
            iteration_counter_name: i
            iterations: 2
            steps:
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration1_mysql_###XXX i XXX###` (`name` varchar(255) NOT NULL);'
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration2_mysql_###XXX i XXX###` (`name` varchar(255) NOT NULL);'

            YAML,
            [
                new SQLExecuteStep([
                    new Query(
                        'mysql',
                        'CREATE TABLE `test_migration1_mysql_0` (`name` varchar(255) NOT NULL);'
                    ),
                ]),
                new SQLExecuteStep([
                    new Query(
                        'mysql',
                        'CREATE TABLE `test_migration2_mysql_0` (`name` varchar(255) NOT NULL);'
                    ),
                ]),
                new SQLExecuteStep([
                    new Query(
                        'mysql',
                        'CREATE TABLE `test_migration1_mysql_1` (`name` varchar(255) NOT NULL);'
                    ),
                ]),
                new SQLExecuteStep([
                    new Query(
                        'mysql',
                        'CREATE TABLE `test_migration2_mysql_1` (`name` varchar(255) NOT NULL);'
                    ),
                ]),
            ],
        ];

        yield 'iteration_counter_starting_value: 42' => [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 1
            iteration_counter_starting_value: 42
            steps:
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration1_mysql_###XXX i XXX###` (`name` varchar(255) NOT NULL);'
                -
                    type: sql
                    mode: execute
                    query:
                        -
                            driver: mysql
                            sql: 'CREATE TABLE `test_migration2_mysql_###XXX i XXX###` (`name` varchar(255) NOT NULL);'

            YAML,
            [
                new SQLExecuteStep([
                    new Query(
                        'mysql',
                        'CREATE TABLE `test_migration1_mysql_42` (`name` varchar(255) NOT NULL);'
                    ),
                ]),
                new SQLExecuteStep([
                    new Query(
                        'mysql',
                        'CREATE TABLE `test_migration2_mysql_42` (`name` varchar(255) NOT NULL);'
                    ),
                ]),
            ],
        ];
    }

    /**
     * @return iterable<array{string, class-string<\Throwable>, 2?: string}>
     */
    public function provideFailingData(): iterable
    {
        yield [
            <<<YAML
            type: repeatable
            mode: create
            YAML,
            \LogicException::class,
            'Expected the key "iterations" to exist.',
        ];

        yield [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 10
            YAML,
            \LogicException::class,
            'Expected the key "steps" to exist.',
        ];
        yield [
            <<<YAML
            type: repeatable
            mode: create
            iterations: 10
            steps: test
            YAML,
            \LogicException::class,
            'Expected an array. Got: string',
        ];
    }
}
