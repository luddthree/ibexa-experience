<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use PHPUnit\Framework\Assert;

final class CurrenciesPage extends Page
{
    private TableInterface $table;

    private Dialog $dialog;

    public function __construct(
        Session $session,
        Router $router,
        TableBuilder $tableBuilder,
        Dialog $dialog
    ) {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
        $this->dialog = $dialog;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('productsPageHeader'))->assert()->textEquals('Currencies');
    }

    public function isCurrencyOnTheList(string $currencyCode): bool
    {
        return $this->table->hasElement(['Code' => $currencyCode]);
    }

    public function edit(string $currencyCode): void
    {
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());
        $this->table->getTableRow(['Code' => $currencyCode])->edit();
    }

    public function delete(string $currencyCode): void
    {
        $this->table->getTableRow(['Code' => $currencyCode])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function verifyEnabledCurrency(string $currencyCode): void
    {
        Assert::assertTrue(
            $this->table->getTableRow(['Code' => $currencyCode])->getCell('Enabled')
                 ->find(new VisibleCSSLocator('input', 'input'))->hasAttribute('checked')
        );
    }

    public function verifyDisabledCurrency(string $currencyCode): void
    {
        Assert::assertFalse(
            $this->table->getTableRow(['Code' => $currencyCode])->getCell('Enabled')
                ->find(new VisibleCSSLocator('input', 'input'))->hasAttribute('checked')
        );
    }

    public function searchForCurrency(string $currencyCode): void
    {
        $this->getHTMLPage()->find($this->getLocator('searchInput'))->setValue($currencyCode);
        $this->getHTMLPage()->find($this->getLocator('searchButton'))->click();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('productsPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash,button[data-bs-original-title^="Delete"]'),
            new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container'),
            new VisibleCSSLocator('searchInput', '.ibexa-pc-search__form-input'),
            new VisibleCSSLocator('searchButton', '.ibexa-pc-search__form .ibexa-input-text-wrapper__action-btn--search'),
        ];
    }

    public function getName(): string
    {
        return 'Currencies';
    }

    protected function getRoute(): string
    {
        return '/currencies/list';
    }
}
