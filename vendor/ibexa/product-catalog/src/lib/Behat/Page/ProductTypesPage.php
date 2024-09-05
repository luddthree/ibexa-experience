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
use Ibexa\ProductCatalog\Behat\Component\TypeOfProductTypeSelect;

final class ProductTypesPage extends Page
{
    private Dialog $dialog;

    private TableInterface $table;

    private TypeOfProductTypeSelect $typeSelect;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder, Dialog $dialog, TypeOfProductTypeSelect $typeSelect)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
        $this->dialog = $dialog;
        $this->typeSelect = $typeSelect;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('productTypesPageHeader'))->assert()->textEquals('Product Types');
    }

    public function isProductTypeOnTheList(string $productTypeName): bool
    {
        return $this->table->hasElement(['Name' => $productTypeName]);
    }

    public function edit(string $productTypeName): void
    {
        $this->table->getTableRow(['Name' => $productTypeName])->edit();
    }

    public function delete(string $contentTypeName): void
    {
        $this->table->getTableRow(['Name' => $contentTypeName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function selectTypeOfProductType(string $typeOfProductType): void
    {
        $this->typeSelect->verifyIsLoaded();
        $this->typeSelect->selectType($typeOfProductType);
        $this->typeSelect->confirm();
    }

    public function getName(): string
    {
        return 'Product Types';
    }

    protected function getRoute(): string
    {
        return '/product-type/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('productTypesPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash,button[data-original-title^="Delete"]'),
        ];
    }
}
