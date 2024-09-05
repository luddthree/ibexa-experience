<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Gateway;

use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\CustomerGroupPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface;
use Ibexa\Contracts\ProductCatalog\Values\Price\Update\Struct\ProductPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\PriceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductInterface;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Gateway\DoctrineDatabase;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CurrencyFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\AbstractDoctrineDatabaseTest;
use Money\Currency;
use Money\Exception\UnknownCurrencyException;
use Money\Money;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Gateway\DoctrineDatabase
 *
 * @phpstan-import-type Data from \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Gateway\DoctrineDatabase
 *
 * @phpstan-extends \Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\AbstractDoctrineDatabaseTest<Data>
 */
final class DoctrineDatabaseTest extends AbstractDoctrineDatabaseTest
{
    protected function setUp(): void
    {
        parent::setUp();

        self::setAdministratorUser();
    }

    protected function getDoctrineDatabaseService(): DoctrineDatabase
    {
        return self::getServiceByClassName(DoctrineDatabase::class);
    }

    public function testInsertingNotConfiguredCurrency(): void
    {
        $currency = $this->createMock(CurrencyInterface::class);

        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::atLeastOnce())
            ->method('getCode')
            ->willReturn('test_code');

        $struct = new ProductPriceCreateStruct(
            $product,
            $currency,
            new Money('4200', new Currency('FOO')),
            null,
            null
        );

        $database = $this->getDoctrineDatabaseService();
        $this->expectException(UnknownCurrencyException::class);
        $this->expectExceptionMessage('Cannot find currency FOO');
        $database->insert($struct);
    }

    /**
     * @dataProvider provideForTestInsert
     */
    public function testInsert(ProductPriceCreateStructInterface $struct, ?callable $assertions = null): void
    {
        $initialCount = self::countPriceRows();

        $database = $this->getDoctrineDatabaseService();
        $id = $database->insert($struct);

        self::assertSame($initialCount + 1, self::countPriceRows());
        $row = self::getRowById($id);

        self::assertNotNull($row);
        self::assertEquals($id, $row['id']);
        self::assertEquals(CurrencyFixture::USD_ID, $row['currency_id']);
        self::assertSame('test_code', $row['product_code']);
        self::assertStringStartsWith('4.2', (string)$row['amount']);
        self::assertEquals('15.55', $row['custom_price_rule']);

        if ($assertions !== null) {
            $assertions($row);
        }
    }

    /**
     * @phpstan-return iterable<array{
     *     \Ibexa\Contracts\ProductCatalog\Values\Price\Create\Struct\ProductPriceCreateStructInterface,
     *     1?: callable(array $row): void,
     * }>
     */
    public function provideForTestInsert(): iterable
    {
        $currency = $this->createMock(CurrencyInterface::class);
        $currency->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(CurrencyFixture::USD_ID);

        $product = $this->createMock(ProductInterface::class);
        $product->expects(self::atLeastOnce())
            ->method('getCode')
            ->willReturn('test_code');

        $money = new Money('4200', new Currency('USD'));
        $customPriceMoney = new Money('2100', new Currency('USD'));

        $struct = new ProductPriceCreateStruct(
            $product,
            $currency,
            $money,
            $customPriceMoney,
            '15.55'
        );

        yield [
            $struct,
        ];

        $customerGroup = $this->createMock(CustomerGroupInterface::class);
        $customerGroup->expects(self::atLeastOnce())
            ->method('getId')
            ->willReturn(CustomerGroupFixture::FIXTURE_ENTRY_ID);

        $struct = new CustomerGroupPriceCreateStruct(
            $customerGroup,
            $product,
            $currency,
            $money,
            $customPriceMoney,
            '15.55'
        );

        yield [
            $struct,
            static function (array $row): void {
                $stmt = self::getDoctrineConnection()->executeQuery(
                    'SELECT * FROM ibexa_product_specification_price_customer_group WHERE id = ?',
                    [$row['id']],
                );

                $result = $stmt->fetchAssociative();

                self::assertIsArray($result);
                self::assertEquals(CustomerGroupFixture::FIXTURE_ENTRY_ID, $result['customer_group_id']);
                self::assertEquals($row['id'], $result['id']);
            },
        ];
    }

    public function testUpdate(): void
    {
        $initialCount = self::countPriceRows();
        $id = 2;

        // Workaround for architectural bug: reused same structs for both: API and Persistence layers
        $price = $this->createMock(PriceInterface::class);
        $price->method('getId')->willReturn($id);

        $struct = new ProductPriceUpdateStruct(
            $price,
            new Money('4200', new Currency('EUR')),
            null,
            '16.55'
        );

        $database = $this->getDoctrineDatabaseService();
        $database->update($struct);

        self::assertSame($initialCount, self::countPriceRows());
        $row = self::getRowById($id);

        self::assertNotNull($row);
        self::assertEquals($id, $row['id']);
        self::assertEquals(CurrencyFixture::EUR_ID, $row['currency_id']);
        self::assertEquals('test_code', $row['product_code']);
        self::assertNull($row['custom_price_amount']);
        self::assertEquals('16.55', $row['custom_price_rule']);
        self::assertStringStartsWith('42', (string)$row['amount']);
    }

    public function testFindByRespectOrder(): void
    {
        $database = $this->getDoctrineDatabaseService();

        $result = $database->findBy([
            'product_code' => 'test_code',
        ], ['id' => 'DESC']);
        self::assertCount(2, $result);
        self::assertLessThan(
            $result[0]['id'],
            $result[1]['id'],
        );

        $result = $database->findBy([
            'product_code' => 'test_code',
        ], ['id' => 'ASC']);
        self::assertCount(2, $result);
        self::assertGreaterThan(
            $result[0]['id'],
            $result[1]['id'],
        );
    }

    public function testDelete(): void
    {
        $initialCount = self::countPriceRows();

        $row = self::getFirstRowByCode('test_code');
        self::assertNotNull($row);
        self::assertArrayHasKey('id', $row);

        $id = (int)$row['id'];

        $database = $this->getDoctrineDatabaseService();
        $database->delete($id);

        self::assertSame($initialCount - 1, self::countPriceRows());
        $row = self::getRowById($id);
        self::assertNull($row);
    }

    private static function countPriceRows(): int
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();

        $qb->select($connection->getDatabasePlatform()->getCountExpression('id'));
        $qb->from('ibexa_product_specification_price');
        $driverStatement = $qb->execute();
        assert($driverStatement instanceof DriverResultStatement);
        $result = $driverStatement->fetchOne();

        if ($result === false) {
            return 0;
        }

        return (int) $result;
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function getFirstRowByCode(string $code): ?array
    {
        foreach (self::getRowsByCode($code) as $row) {
            return $row;
        }

        return null;
    }

    /**
     * @return iterable<int, array<string, mixed>>
     */
    private static function getRowsByCode(string $code): iterable
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();

        $qb->select('ibexa_product_specification_price.*');
        $qb->from('ibexa_product_specification_price');
        $qb->where($qb->expr()->eq(
            'ibexa_product_specification_price.product_code',
            $qb->createPositionalParameter($code),
        ));

        $stmt = $qb->execute();
        assert($stmt instanceof DriverResultStatement);

        while ($row = $stmt->fetchAssociative()) {
            yield $row;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function getRowById(int $id): ?array
    {
        $connection = self::getDoctrineConnection();
        $qb = $connection->createQueryBuilder();

        $qb->select('ibexa_product_specification_price.*');
        $qb->from('ibexa_product_specification_price');
        $qb->where($qb->expr()->eq(
            'ibexa_product_specification_price.id',
            $qb->createPositionalParameter($id),
        ));

        $stmt = $qb->execute();
        assert($stmt instanceof DriverResultStatement);

        $result = $stmt->fetchAssociative();
        if ($result === false) {
            return null;
        }

        return $result;
    }

    public function provideForFindAllTest(): iterable
    {
        yield [
            static function (array $results, int $count): void {
                self::assertSame(10, $count);
                self::assertCount(10, $results);

                foreach ($results as $row) {
                    self::assertIsInt($row['id']);
                    self::assertIsString($row['product_code']);
                    self::assertContains(
                        $row['currency_id'],
                        CurrencyFixture::CURRENCY_IDS,
                    );
                    self::assertIsNumeric($row['amount']);

                    if ($row['discriminator'] === 'customer_group') {
                        self::assertIsInt($row['customer_group_customer_group_id']);
                    }
                }
            },
        ];
    }

    public function provideForFindByTest(): iterable
    {
        yield 'Return all prices for "test_code" product when no limit is specified' => [
            static function (array $results, int $count): void {
                self::assertSame(2, $count);
                self::assertCount(2, $results);
            },
            [
                'product_code' => 'test_code',
            ],
        ];

        yield 'Return a single entry when limit set to 1' => [
            static function (array $results, int $count): void {
                self::assertSame(2, $count);
                self::assertCount(1, $results);
            },
            [
                'product_code' => 'test_code',
            ],
            [1],
        ];

        yield 'No results when trying to get "test_code" price with offset (2) equal row count (2)' => [
            static function (array $results, int $count): void {
                self::assertSame(2, $count);
                self::assertCount(0, $results);
            },
            [
                'product_code' => 'test_code',
            ],
            [null, 2],
        ];

        yield 'Return Customer Group price data when "customer_group" discriminator is used' => [
            static function (array $results, int $count): void {
                self::assertSame(4, $count);
                self::assertCount(4, $results);
            },
            [
                'discriminator' => 'customer_group',
            ],
        ];

        yield 'Return only Main price data when "main" discriminator is used' => [
            static function (array $results, int $count): void {
                self::assertSame(6, $count);
                self::assertCount(6, $results);
            },
            [
                'discriminator' => 'main',
            ],
        ];
    }

    public function provideForFindByIdTest(): iterable
    {
        $id = 1;

        yield [
            static function (?array $result): void {
                self::assertIsArray($result);
                self::assertArrayHasKey('id', $result);
                self::assertArrayHasKey('product_code', $result);
                self::assertArrayHasKey('amount', $result);
                self::assertArrayHasKey('currency_id', $result);
            },
            $id,
        ];

        $id = 4;

        yield [
            static function (?array $result): void {
                self::assertIsArray($result);
                self::assertArrayHasKey('id', $result);
                self::assertArrayHasKey('product_code', $result);
                self::assertArrayHasKey('amount', $result);
                self::assertArrayHasKey('currency_id', $result);
                self::assertArrayHasKey('customer_group_customer_group_id', $result);
            },
            $id,
        ];
    }
}
