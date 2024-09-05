<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Persistence\Legacy\ProductPrice;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Mapper;
use Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice;
use Ibexa\ProductCatalog\Local\Persistence\Values\ProductPrice\CustomerGroupPrice;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CurrencyFixture;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CustomerGroupFixture;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Persistence\Legacy\ProductPrice\Mapper
 */
final class MapperTest extends IbexaKernelTestCase
{
    private const AMOUNT = '42.00';

    private Mapper $mapper;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->mapper = self::getServiceByClassName(Mapper::class);
    }

    /**
     * @dataProvider provideForCreate
     *
     * @phpstan-param array{
     *     id: int,
     *     amount: numeric-string,
     *     custom_price_amount: numeric-string|null,
     *     custom_price_rule: numeric-string|null,
     *     currency_id: int,
     *     product_code: non-empty-string,
     *     discriminator: string,
     *     customer_group_customer_group_id?: int,
     * } $data
     * @phpstan-param callable(\Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice $price): void $expectations
     */
    public function testCreateFromRow(array $data, callable $expectations): void
    {
        $price = $this->mapper->createFromRow($data);

        self::assertSame(42, $price->getId());
        self::assertSame(self::AMOUNT, $price->getAmount());
        self::assertSame('FOO', $price->getProductCode());
        $expectations($price);
    }

    /**
     * @phpstan-return iterable<
     *     array{
     *         array{
     *             id: int,
     *             amount: numeric-string|null,
     *             custom_price_amount: numeric-string|null,
     *             custom_price_rule: numeric-string|null,
     *             currency_id: int,
     *             product_code: non-empty-string,
     *             discriminator: string,
     *             customer_group_customer_group_id?: int,
     *         },
     *         callable(\Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice $price): void,
     *     },
     * >
     */
    public function provideForCreate(): iterable
    {
        $baseline = [
            'id' => 42,
            'amount' => self::AMOUNT,
            'custom_price_amount' => null,
            'custom_price_rule' => null,
            'product_code' => 'FOO',
            'currency_id' => CurrencyFixture::BTC_ID,
            'discriminator' => 'main',
        ];

        yield [
            $baseline,
            static function (AbstractProductPrice $price): void {
                self::assertInstanceOf(ProductPrice::class, $price);
            },
        ];

        yield [
            [
                'discriminator' => 'customer_group',
                'customer_group_customer_group_id' => CustomerGroupFixture::FIXTURE_ENTRY_ID,
            ] + $baseline,
            static function (AbstractProductPrice $price): void {
                self::assertInstanceOf(CustomerGroupPrice::class, $price);
            },
        ];
    }

    /**
     * @dataProvider provideForCreate
     *
     * @phpstan-param array{
     *     id: int,
     *     amount: numeric-string,
     *     custom_price_amount: numeric-string|null,
     *     custom_price_rule: numeric-string|null,
     *     currency_id: int,
     *     product_code: non-empty-string,
     *     discriminator: string,
     *     customer_group_customer_group_id?: int,
     * } $data
     * @phpstan-param callable(\Ibexa\ProductCatalog\Local\Persistence\Values\AbstractProductPrice $price): void $expectations
     */
    public function testCreateFromRows(array $data, callable $expectations): void
    {
        $rows = [
            $data,
        ];

        [$price] = $this->mapper->createFromRows($rows);

        self::assertSame(42, $price->getId());
        self::assertSame(self::AMOUNT, $price->getAmount());
        self::assertSame('FOO', $price->getProductCode());
        $expectations($price);
    }
}
