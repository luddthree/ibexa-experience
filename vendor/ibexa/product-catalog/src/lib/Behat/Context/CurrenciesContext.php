<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\ProductCatalog\Behat\Page\CurrenciesPage;
use Ibexa\ProductCatalog\Behat\Page\CurrencyUpdatePage;
use PHPUnit\Framework\Assert;

final class CurrenciesContext implements Context
{
    private CurrenciesPage $currenciesPage;

    private CurrencyUpdatePage $currencyUpdatePage;

    public function __construct(CurrenciesPage $currenciesPage, CurrencyUpdatePage $currencyUpdatePage)
    {
        $this->currenciesPage = $currenciesPage;
        $this->currencyUpdatePage = $currencyUpdatePage;
    }

    /**
     * @Given there's a :currencyCode on Currencies list
     */
    public function thereIsOnCurrenciesList(string $currencyCode): void
    {
        Assert::assertTrue($this->currenciesPage->isCurrencyOnTheList($currencyCode));
    }

    /**
     * @Given there's no :currencyCode on Currencies list
     */
    public function thereIsNoOnCurrenciesList(string $currencyCode): void
    {
        Assert::assertFalse($this->currenciesPage->isCurrencyOnTheList($currencyCode));
    }

    /**
     * @When I start editing Currency :currencyCode
     */
    public function iStartEditingItem(string $currencyCode): void
    {
        $this->currenciesPage->edit($currencyCode);
    }

    /**
     * @When I delete :currencyCode Currency
     */
    public function iDeleteCurrency(string $currencyCode): void
    {
        $this->currenciesPage->delete($currencyCode);
    }

    /**
     * @Then I enable currency
     */
    public function iEnableCurrency(): void
    {
        $this->currencyUpdatePage->enableCurrency();
    }

    /**
     * @Then Currency :currencyCode is enabled
     */
    public function iVerifyEnabledCurrency(string $currencyCode): void
    {
        $this->currenciesPage->verifyEnabledCurrency($currencyCode);
    }

    /**
     * @Then Currency :currencyCode is disabled
     */
    public function iVerifyDisabledCurrency(string $currencyCode): void
    {
        $this->currenciesPage->verifyDisabledCurrency($currencyCode);
    }

    /**
     * @Then I search for a :currencyCode Currency
     */
    public function iSearchForCurrency(string $currencyCode): void
    {
        $this->currenciesPage->searchForCurrency($currencyCode);
    }
}
