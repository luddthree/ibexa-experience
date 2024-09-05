<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\Query\Criterion\CurrencyCodeCriterion;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStep;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Currency\CurrencyUpdateStepExecutor
 */
final class CurrencyUpdateStepExecutorTest extends AbstractStepExecutorTest
{
    private CurrencyServiceInterface $currencyService;

    private CurrencyUpdateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->currencyService = self::getServiceByClassName(CurrencyServiceInterface::class);

        $this->executor = new CurrencyUpdateStepExecutor($this->currencyService);
        $this->configureExecutor($this->executor);
    }

    public function testHandle(): void
    {
        try {
            $this->currencyService->getCurrencyByCode('FOO');
            self::fail('Currency FOO already exists');
        } catch (NotFoundException $e) {
            // Expected
        }

        $currency = $this->currencyService->getCurrencyByCode('EUR');
        self::assertSame('EUR', $currency->getCode());
        self::assertSame(2, $currency->getSubUnits());
        self::assertTrue($currency->isEnabled());

        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new CurrencyUpdateStep(
            new CurrencyCodeCriterion('EUR'),
            'FOO',
            4,
            false,
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $currency = $this->currencyService->getCurrencyByCode('FOO');
        self::assertSame('FOO', $currency->getCode());
        self::assertFalse($currency->isEnabled());
        self::assertSame(4, $currency->getSubUnits());
    }
}
