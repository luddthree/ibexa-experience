<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway;

use Ibexa\Contracts\CorePersistence\Exception\RuntimeMappingExceptionInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\DoctrineDatabase;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;
use InvalidArgumentException;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\Gateway\DoctrineDatabase
 */
final class DoctrineDatabaseTest extends IbexaKernelTestCase
{
    private const FIXTURE_ENTRY_ID = 42;

    private DoctrineDatabase $database;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->database = self::getServiceByClassName(DoctrineDatabase::class);
    }

    /**
     * @depends testFindById
     */
    public function testInsert(): void
    {
        $rowCount = self::getTableRowCount();

        $createStruct = new CustomerGroupCreateStruct(
            'foo',
            [
                2 => 'Foo',
            ],
            [
                2 => 'Lorem Ipsum',
            ],
            '42.5',
        );

        $id = $this->database->insert($createStruct);

        self::assertTableRowCount($rowCount + 1);

        $row = $this->database->findById($id);
        self::assertNotNull($row);
        self::assertIsInt($row['id']);
        self::assertSame('foo', $row['identifier']);
        self::assertStringStartsWith('42.5', $row['global_price_rate']);
    }

    /**
     * @depends testFindById
     */
    public function testUpdate(): void
    {
        $id = self::FIXTURE_ENTRY_ID;
        $rowCount = self::getTableRowCount();

        $updateStruct = new CustomerGroupUpdateStruct(
            $id,
            'bar',
            [
                2 => 'Bar',
            ],
            [
                2 => 'dolor sit amet',
            ],
            '42.5',
        );

        $this->database->update($updateStruct);

        self::assertTableRowCount($rowCount);

        $row = $this->database->findById($id);
        self::assertNotNull($row);
        self::assertIsInt($row['id']);
        self::assertSame('bar', $row['identifier']);
        self::assertStringStartsWith('42.5', $row['global_price_rate']);
    }

    public function testDelete(): void
    {
        $id = self::FIXTURE_ENTRY_ID;
        $rowCount = self::getTableRowCount();

        $this->database->delete($id);
        self::assertTableRowCount($rowCount - 1);
    }

    public function testFindById(): void
    {
        $row = $this->database->findById(self::FIXTURE_ENTRY_ID);

        self::assertNotNull($row);
        self::assertSame(42, $row['id']);
        self::assertSame('answer to everything', $row['identifier']);
        self::assertStringStartsWith('66.6', $row['global_price_rate']);
    }

    public function testReturnNullWhenFindingNonExistentId(): void
    {
        $row = $this->database->findById(-1);
        self::assertNull($row);
    }

    public function testFindingWithCriteria(): void
    {
        self::assertCount(1, $this->database->findBy(['id' => self::FIXTURE_ENTRY_ID]));
        self::assertCount(1, $this->database->findBy(['identifier' => 'answer to everything']));
        self::assertCount(1, $this->database->findBy(['name' => 'Answer To Life, Universe and Everything']));
        self::assertCount(1, $this->database->findBy(['description' => '']));
        self::assertCount(1, $this->database->findBy(['global_price_rate' => '66.6']));
    }

    public function testFindWithLimit(): void
    {
        for ($i = 0; $i < 98; ++$i) {
            $this->database->insert(new CustomerGroupCreateStruct(
                sprintf('customer_group_%03d', $i),
                [
                    2 => sprintf('customer_group_%03d', $i),
                ],
                [],
            ));
        }

        self::assertCount(103, $this->database->findAll());
        self::assertCount(50, $this->database->findAll(50));
        self::assertCount(3, $this->database->findAll(null, 100));

        self::assertCount(103, $this->database->findBy([]));
        self::assertCount(50, $this->database->findBy([], null, 50));
        self::assertCount(3, $this->database->findBy([], null, null, 100));
    }

    public function testThrowsWhenFindingUsingInvalidColumnInCriterion(): void
    {
        $this->expectException(RuntimeMappingExceptionInterface::class);
        $this->expectExceptionMessage(
            'Column "non-existent-column" does not exist in "ibexa_customer_group" table. Available columns: "id", '
            . '"identifier", "global_price_rate", "id(translation)", "customer_group_id(translation)", '
            . '"language_id(translation)", "name(translation)", "name_normalized(translation)", "description(translation)"'
        );
        $this->database->findBy(['non-existent-column' => '']);
    }

    public function testThrowsWhenFindingUsingInvalidColumnInOrderBy(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            '"non-existent-column" does not exist in "ibexa_customer_group", or is not available for ordering. '
            . 'Available columns are: "id", "identifier", "global_price_rate"'
        );
        /** @phpstan-ignore-next-line */
        $this->database->findBy([], ['non-existent-column' => '']);
    }

    public function testFindAll(): void
    {
        $count = self::getTableRowCount();
        $rows = $this->database->findAll();

        self::assertCount($count, $rows);
    }

    private static function assertTableRowCount(int $expectedCount): void
    {
        $result = self::getTableRowCount();

        self::assertSame($expectedCount, $result);
    }

    private static function getTableRowCount(): int
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();
        $qb->select($connection->getDatabasePlatform()->getCountExpression('icg.id'))
            ->from('ibexa_customer_group', 'icg');
        $result = $qb->execute()->fetchOne();

        return (int) $result;
    }
}
