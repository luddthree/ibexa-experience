<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Tests\Integration\ProductCatalog\Fixtures\CurrencyFixture;

/**
 * @covers \Ibexa\ProductCatalog\Local\Repository\CurrencyService
 *
 * @group currency-service
 */
final class CurrencyServiceAuthorizationTest extends BaseCurrencyServiceTest
{
    public function testCreateThrowsUnauthorizedException(): void
    {
        $service = self::getCurrencyService();

        $struct = self::getCurrencyCreateStruct();

        self::setAnonymousUser();

        $this->expectUnauthorizedException();

        $service->createCurrency($struct);
    }

    public function testUpdateThrowsUnauthorizedException(): void
    {
        $service = self::getCurrencyService();

        $currency = $service->getCurrency(CurrencyFixture::USD_ID);

        self::setAnonymousUser();
        $this->expectUnauthorizedException();

        $service->updateCurrency($currency, new CurrencyUpdateStruct());
    }

    public function testDeleteThrowsUnauthorizedException(): void
    {
        $service = self::getCurrencyService();
        $currency = $service->getCurrency(CurrencyFixture::EUR_ID);

        self::setAnonymousUser();

        $this->expectUnauthorizedException();

        $service->deleteCurrency($currency);
    }

    private function expectUnauthorizedException(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessageMatches('/\'currency\' \'commerce\'/');
    }
}
