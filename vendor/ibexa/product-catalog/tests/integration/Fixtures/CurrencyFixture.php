<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Fixtures;

use Ibexa\Contracts\Core\Test\Persistence\Fixture;

final class CurrencyFixture implements Fixture
{
    public const EUR_ID = 1;
    public const USD_ID = 2;
    public const BTC_ID = 3;

    public const CURRENCY_IDS = [
        self::EUR_ID,
        self::USD_ID,
        self::BTC_ID,
    ];

    /**
     * @return array<string, array<array<string, scalar>>>
     */
    public function load(): array
    {
        return [
            'ibexa_currency' => [
                [
                    'id' => self::EUR_ID,
                    'code' => 'EUR',
                    'subunits' => 2,
                ],
                [
                    'id' => self::USD_ID,
                    'code' => 'USD',
                    'subunits' => 3,
                ],
                [
                    'id' => self::BTC_ID,
                    'code' => 'BTC',
                    'subunits' => 4,
                ],
            ],
        ];
    }
}
