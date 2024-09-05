<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Fixtures;

use Ibexa\Contracts\Core\Test\Persistence\Fixture;

final class ProductPriceFixture implements Fixture
{
    /**
     * @return array<string, array<array<string, scalar>>>
     */
    public function load(): array
    {
        $data = [
            'ibexa_product_specification_price' => [
                [
                    'id' => 1,
                    'product_code' => 'test_code',
                    'currency_id' => CurrencyFixture::BTC_ID,
                    'amount' => '100000',
                    'discriminator' => 'main',
                ],
                [
                    'id' => 2,
                    'product_code' => 'test_code',
                    'currency_id' => CurrencyFixture::EUR_ID,
                    'amount' => '100000',
                    'discriminator' => 'main',
                ],
                [
                    'id' => 3,
                    'product_code' => 'foo_code',
                    'currency_id' => CurrencyFixture::EUR_ID,
                    'amount' => '4200',
                    'discriminator' => 'main',
                ],
                [
                    'id' => 4,
                    'product_code' => 'foo_code',
                    'currency_id' => CurrencyFixture::EUR_ID,
                    'amount' => '4200',
                    'discriminator' => 'customer_group',
                ],
            ],
            'ibexa_product_specification_price_customer_group' => [
                [
                    'id' => 4,
                    'customer_group_id' => CustomerGroupFixture::FIXTURE_ENTRY_ID,
                ],
            ],
        ];

        $id = 5;
        foreach (CurrencyFixture::CURRENCY_IDS as $currencyId) {
            $data['ibexa_product_specification_price'][] = [
                'id' => $id,
                'product_code' => 'PRICE_INTEGRATION_TEST',
                'amount' => '42.00',
                'currency_id' => $currencyId,
                'discriminator' => 'main',
            ];

            ++$id;

            $data['ibexa_product_specification_price'][] = [
                'id' => $id,
                'product_code' => 'PRICE_INTEGRATION_TEST',
                'amount' => '42.00',
                'currency_id' => $currencyId,
                'discriminator' => 'customer_group',
            ];

            $data['ibexa_product_specification_price_customer_group'][] = [
                'id' => $id,
                'customer_group_id' => CustomerGroupFixture::FIXTURE_ENTRY_ID,
            ];

            ++$id;
        }

        return $data;
    }
}
