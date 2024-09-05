<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Migration\ValueObject\Step\StepInterface;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStep;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Currency\CurrencyCreateStepExecutor
 */
final class CurrencyCreateStepExecutorTest extends AbstractStepExecutorTest
{
    private CurrencyServiceInterface $currencyService;

    private CurrencyCreateStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->currencyService = self::getServiceByClassName(CurrencyServiceInterface::class);

        $this->executor = new CurrencyCreateStepExecutor($this->currencyService);
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

        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new CurrencyCreateStep(
            'FOO',
            2,
            true,
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $currency = $this->currencyService->getCurrencyByCode('FOO');
        self::assertSame('FOO', $currency->getCode());
        self::assertTrue($currency->isEnabled());
        self::assertSame(2, $currency->getSubUnits());
    }
}
