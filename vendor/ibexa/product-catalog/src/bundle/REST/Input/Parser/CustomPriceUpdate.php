<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\REST\Input\Parser;

use Ibexa\Bundle\ProductCatalog\REST\Value\CustomPriceUpdateStruct;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\Rest\Input\ParsingDispatcher;
use Money\Currency;
use Money\Money;

/**
 * @template-extends \Ibexa\Bundle\ProductCatalog\REST\Input\Parser\AbstractParser<
 *     \Ibexa\Bundle\ProductCatalog\REST\Value\CustomPriceUpdateStruct
 * >
 */
final class CustomPriceUpdate extends AbstractParser
{
    private const CURRENCY_KEY = 'currency';
    private const AMOUNT_KEY = 'amount';
    private const CUSTOM_AMOUNT_KEY = 'customAmount';
    private const CUSTOM_AMOUNT_RULE_KEY = 'customAmountRule';

    private CurrencyServiceInterface $currencyService;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function doParse(array $data, ParsingDispatcher $parsingDispatcher): CustomPriceUpdateStruct
    {
        $currency = $this->currencyService->getCurrencyByCode(
            $data[self::CURRENCY_KEY]
        );

        $amount = null;
        if (isset($data[self::AMOUNT_KEY])) {
            $amount = new Money(
                $data[self::AMOUNT_KEY],
                new Currency($currency->getCode())
            );
        }

        $customAmount = null;
        if (isset($data[self::CUSTOM_AMOUNT_KEY])) {
            $customAmount = new Money(
                $data[self::CUSTOM_AMOUNT_KEY],
                new Currency($currency->getCode())
            );
        }

        $customAmountRule = null;
        if (isset($data[self::CUSTOM_AMOUNT_RULE_KEY])) {
            $customAmountRule = $data[self::CUSTOM_AMOUNT_RULE_KEY];
        }

        return new CustomPriceUpdateStruct(
            $amount,
            $customAmount,
            $customAmountRule
        );
    }

    public function getRequiredKeys(): array
    {
        return [self::CURRENCY_KEY];
    }

    public function getOptionalKeys(): array
    {
        return [self::AMOUNT_KEY, self::CUSTOM_AMOUNT_KEY, self::CUSTOM_AMOUNT_RULE_KEY];
    }

    public function getIdentifier(): string
    {
        return 'CustomPriceUpdate';
    }
}
