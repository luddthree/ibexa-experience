<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Money\Currency;
use Money\Money;

/**
 * @template-extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AbstractParser<
 *     \Ibexa\Bundle\ProductCatalog\REST\Value\BasePriceCreateStruct
 * >
 */
final class BasePriceCreate extends AbstractParser
{
    private const CURRENCY_KEY = 'currency';
    private const AMOUNT_KEY = 'amount';

    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function doParse(array $data, ParsingDispatcher $parsingDispatcher): Value\BasePriceCreateStruct
    {
        $currency = $this->currencyService->getCurrencyByCode(
            $data[self::CURRENCY_KEY]
        );

        $amount = new Money(
            $data[self::AMOUNT_KEY],
            new Currency($currency->getCode())
        );

        return new Value\BasePriceCreateStruct($currency, $amount);
    }

    public function getRequiredKeys(): array
    {
        return [self::CURRENCY_KEY, self::AMOUNT_KEY];
    }

    public function getIdentifier(): string
    {
        return 'PriceCreateStruct';
    }
}
