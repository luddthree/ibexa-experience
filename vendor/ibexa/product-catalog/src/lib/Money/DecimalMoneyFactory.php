<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Money;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\CurrencyServiceInterface;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\CurrencyFetchAdapter;
use Ibexa\Core\MVC\Symfony\MVCEvents;
use Money\Currencies\CurrencyList;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Parser\DecimalMoneyParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class DecimalMoneyFactory implements EventSubscriberInterface
{
    private const CURRENCIES_LOAD_BATCH_SIZE = 200;

    private CurrencyServiceInterface $currencyService;

    private ?CurrencyList $currencies;

    private ?DecimalMoneyParser $moneyParser;

    private ?DecimalMoneyFormatter $moneyFormatter;

    public function __construct(CurrencyServiceInterface $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MVCEvents::CONFIG_SCOPE_CHANGE => 'reset',
        ];
    }

    public function reset(): void
    {
        $this->moneyParser = null;
        $this->moneyFormatter = null;
        $this->currencies = null;
    }

    public function getMoneyParser(): DecimalMoneyParser
    {
        if (!isset($this->moneyParser)) {
            $currencies = $this->loadCurrencies();
            $this->moneyParser = new DecimalMoneyParser($currencies);
        }

        return $this->moneyParser;
    }

    public function getMoneyFormatter(): DecimalMoneyFormatter
    {
        if (!isset($this->moneyFormatter)) {
            $currencies = $this->loadCurrencies();
            $this->moneyFormatter = new DecimalMoneyFormatter($currencies);
        }

        return $this->moneyFormatter;
    }

    private function loadCurrencies(): CurrencyList
    {
        if (!isset($this->currencies)) {
            $currencyCodeToSubunitsMap = [];

            /** @var iterable<\Ibexa\Contracts\ProductCatalog\Values\CurrencyInterface> $currencies */
            $currencies = new BatchIterator(
                new CurrencyFetchAdapter($this->currencyService),
                self::CURRENCIES_LOAD_BATCH_SIZE
            );

            foreach ($currencies as $currency) {
                $currencyCodeToSubunitsMap[$currency->getCode()] = $currency->getSubUnits();
            }

            $this->currencies = new CurrencyList($currencyCodeToSubunitsMap);
        }

        return $this->currencies;
    }
}
