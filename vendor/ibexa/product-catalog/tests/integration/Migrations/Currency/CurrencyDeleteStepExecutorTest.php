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
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStep;
use Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStepExecutor;
use Ibexa\Tests\Integration\ProductCatalog\Migrations\AbstractStepExecutorTest;

/**
 * @covers \Ibexa\ProductCatalog\Migrations\Currency\CurrencyDeleteStepExecutor
 */
final class CurrencyDeleteStepExecutorTest extends AbstractStepExecutorTest
{
    private CurrencyServiceInterface $currencyService;

    private CurrencyDeleteStepExecutor $executor;

    protected function setUp(): void
    {
        self::setAdministratorUser();

        $this->currencyService = self::getServiceByClassName(CurrencyServiceInterface::class);

        $this->executor = new CurrencyDeleteStepExecutor($this->currencyService);
        $this->configureExecutor($this->executor);
    }

    public function testHandle(): void
    {
        $this->currencyService->getCurrencyByCode('EUR');

        self::assertFalse($this->executor->canHandle($this->createMock(StepInterface::class)));

        $step = new CurrencyDeleteStep(
            new CurrencyCodeCriterion('EUR'),
        );

        self::assertTrue($this->executor->canHandle($step));
        $this->executor->handle($step);

        $this->expectException(NotFoundException::class);
        $this->currencyService->getCurrencyByCode('FOO');
    }
}
