<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CurrencyCreateStepExecutor extends AbstractStepExecutor
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     */
    protected function doHandle(StepInterface $step): CurrencyInterface
    {
        assert($step instanceof CurrencyCreateStep);

        $struct = new CurrencyCreateStruct(
            $step->getCode(),
            $step->getSubunits(),
            $step->isEnabled(),
        );

        return $this->currencyService->createCurrency($struct);
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CurrencyCreateStep;
    }
}
