<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy;

use Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @template T of array
 */
abstract class AbstractDoctrineDatabaseTest extends IbexaKernelTestCase
{
    /**
     * Provides an instance of service under test.
     *
     * @return \Ibexa\Contracts\CorePersistence\Gateway\AbstractDoctrineDatabase<T>
     */
    abstract protected function getDoctrineDatabaseService(): AbstractDoctrineDatabase;

    /**
     * Provides assertion function to be applied to result set (array of arrays) coming from service under test, and
     * arguments for the `findAll` method call.
     *
     * @phpstan-return iterable<array{
     *     callable(array<array<string, mixed>> $results, int $count): void,
     *     1?: array{0?: int, 1?: int},
     * }>
     */
    abstract public function provideForFindAllTest(): iterable;

    /**
     * Provides assertion function to be applied to result set (array of arrays) coming from service under test, and
     * arguments for the `findBy` method call.
     *
     * @phpstan-return iterable<array{
     *     callable(array<T> $results, int $count): void,
     *     array<string, mixed>,
     *     2?: array{0?: int|null, 1?: int},
     * }>
     */
    abstract public function provideForFindByTest(): iterable;

    /**
     * Provides assertion function to be applied to result set (a single array or null) coming from service under test,
     * and arguments for the `findById` method call.
     *
     * @phpstan-return iterable<array{
     *     callable(T|null): void,
     *     int,
     * }>
     */
    abstract public function provideForFindByIdTest(): iterable;

    protected function setUp(): void
    {
        self::bootKernel();
    }

    /**
     * @dataProvider provideForFindAllTest
     *
     * @param callable(array<T> $results, int $count): void $expectations
     * @param array{0?: int, 1?: int} $arguments
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Ibexa\ProductCatalog\Local\Persistence\Legacy\Exception\MappingException
     */
    public function testFindAll(callable $expectations, array $arguments = []): void
    {
        $database = $this->getDoctrineDatabaseService();
        $results = $database->findAll(...$arguments);
        $count = $database->countAll();
        $expectations($results, $count);
    }

    /**
     * @dataProvider provideForFindByTest
     *
     * @param callable(array<array<string, mixed>> $results, int $count): void $expectations
     * @param array<string, mixed> $criteria
     * @param array{0?: int, 1?: int} $arguments
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Ibexa\ProductCatalog\Local\Persistence\Legacy\Exception\MappingException
     */
    public function testFindBy(callable $expectations, array $criteria, array $arguments = []): void
    {
        $database = $this->getDoctrineDatabaseService();
        $results = $database->findBy($criteria, [], ...$arguments);
        $count = $database->countBy($criteria);
        $expectations($results, $count);
    }

    /**
     * @dataProvider provideForFindByIdTest
     *
     * @param callable(T|null): void $expectations
     * @param int $id
     *
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws \Ibexa\ProductCatalog\Local\Persistence\Legacy\Exception\MappingException
     */
    public function testFindById(callable $expectations, int $id): void
    {
        $database = $this->getDoctrineDatabaseService();
        $result = $database->findById($id);
        $expectations($result);
    }
}
