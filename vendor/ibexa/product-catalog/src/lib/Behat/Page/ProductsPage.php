<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\CreateNewPopup;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

final class ProductsPage extends Page
{
    private Dialog $dialog;

    private TableInterface $table;

    private CreateNewPopup $popup;

    public function __construct(
        Session $session,
        Router $router,
        TableBuilder $tableBuilder,
        Dialog $dialog,
        CreateNewPopup $popup
    ) {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
        $this->dialog = $dialog;
        $this->popup = $popup;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('productsPageHeader'))->assert()->textEquals('Products');
    }

    public function selectProductOptions(string $productType): void
    {
        $this->popup->verifyIsLoaded();
        $this->popup->verifyHeaderText('Create product');
        $this->popup->selectFromDropdown('Language', 'English (United Kingdom)');
        $this->popup->selectFromDropdown('Product type', $productType);
        $this->popup->confirm();
    }

    public function getName(): string
    {
        return 'Products';
    }

    public function isProductTypeOnTheList(string $productName): bool
    {
        return $this->table->hasElement(['Name' => $productName]);
    }

    public function edit(string $productName): void
    {
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());
        $this->table->getTableRow(['Name' => $productName])->edit();
    }

    public function delete(string $productName): void
    {
        $this->table->getTableRow(['Name' => $productName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function isProductWithVariantsOnList(string $productName, string $productCode, string $variantStatus): bool
    {
        return $this->table->hasElement(['Name' => $productName, 'Code' => $productCode, 'Variant' => $variantStatus]);
    }

    protected function getRoute(): string
    {
        return '/product/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('productsPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash,button[data-bs-original-title^="Delete"]'),
            new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container'),
        ];
    }
}
