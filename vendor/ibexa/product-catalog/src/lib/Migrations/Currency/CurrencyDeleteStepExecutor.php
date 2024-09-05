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
use Ibexa\Migration\ValueObject\Step\StepInterface;

final class CurrencyDeleteStepExecutor extends AbstractStepExecutor
{
    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException
     */
    protected function doHandle(StepInterface $step)
    {
        assert($step instanceof CurrencyDeleteStep);

        $query = new CurrencyQuery($step->getCriterion(), [], null);
        $currencyList = $this->currencyService->findCurrencies($query);

        foreach ($currencyList as $currency) {
            $this->currencyService->deleteCurrency($currency);
        }

        return null;
    }

    public function canHandle(StepInterface $step): bool
    {
        return $step instanceof CurrencyDeleteStep;
    }
}
