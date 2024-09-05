<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Local\Repository;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Tests\Integration\ProductCatalog\IbexaKernelTestCase;

abstract class BaseCurrencyServiceTest extends IbexaKernelTestCase
{
    public function setUp(): void
    {
        self::bootKernel();
        self::setAdministratorUser();
    }

    protected static function getCurrencyService(): CurrencyServiceInterface
    {
        return self::getServiceByClassName(CurrencyServiceInterface::class);
    }

    /**
     * @param non-empty-string $code
     * @param int<0, max> $subunits
     * @param bool $enabled
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct
     */
    protected static function getCurrencyCreateStruct(
        string $code = 'foo',
        int $subunits = 2,
        bool $enabled = true
    ): CurrencyCreateStruct {
        return new CurrencyCreateStruct(
            $code,
            $subunits,
            $enabled
        );
    }
}
