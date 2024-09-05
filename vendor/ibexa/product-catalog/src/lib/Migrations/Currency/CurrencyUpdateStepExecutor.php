<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Migrations\Currency;

use Ibexa\Contracts\Migration\StepExecutor\AbstractStepExecutor;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyQuery;
use Ibexa\Contracts\ProductCatalog\Values\Currency\CurrencyUpdateStruct;
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CurrencyUpdateStepExecutor extends AbstractStepExecutor
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     *
     * @return \Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface[]
     */
    protected function doHandle(StepInterface $step): array
    {
        assert($step instanceof CurrencyUpdateStep);

        $struct = new CurrencyUpdateStruct();
        $struct->setCode($step->getCode());
        $struct->setSubunits($step->getSubunits());
        $struct->setEnabled($step->isEnabled());

        $query = new CurrencyQuery($step->getCriterion(), [], null);
        $currencyList = $this->currencyService->findCurrencies($query);

        $currencies = [];
        foreach ($currencyList as $currency) {
            $currencies[] = $this->currencyService->updateCurrency($currency, $struct);
        }

        return $currencies;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CurrencyUpdateStep;
    }
}
