<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class ProductVariantTab extends Component
{
    private TableInterface $table;

    private Dialog $dialog;

    private GenerateProductVariantsPopupWindow $generateProductVariantsPopupWindow;

    public function __construct(Session $session, Dialog $dialog, TableBuilder $tableBuilder, GenerateProductVariantsPopupWindow $generateProductVariantsPopupWindow)
    {
        parent::__construct($session);
        $this->dialog = $dialog;
        $this->generateProductVariantsPopupWindow = $generateProductVariantsPopupWindow;
        $this->table = $tableBuilder->newTable()->build();
    }

    public function clickOnGenerateVariantsButton(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('generateVariantsButton'))->click();
        $this->generateProductVariantsPopupWindow->verifyIsLoaded();
    }

    public function isProductVariantOnTheList(string $productVariantName, string $productCode): bool
    {
        return $this->table->hasElement(['Name' => $productVariantName, 'Product code' => $productCode]);
    }

    public function addVariant(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('addButton'))->click();
    }

    public function deleteVariant(string $productVariantName): void
    {
        $this->table->getTableRow(['Name' => $productVariantName])->select();
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function edit(string $productVariantName): void
    {
        $this->table->getTableRow(['Name' => $productVariantName])->edit();
    }

    public function delete(): void
    {
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function getEmptyTableInformation(): string
    {
        return $this->getHTMLPage()->find($this->getLocator('emptyVariantsTableInfo'))->getText();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('variantTab'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('variantTab', '#ibexa-tab-product-variants'),
            new VisibleCSSLocator('generateVariantsButton', '.ibexa-btn--prevented'),
            new VisibleCSSLocator('deleteButton', '.ibexa-icon--trash,button[data-bs-original-title^="Delete"]'),
            new VisibleCSSLocator('addButton', '.ibexa-btn--tertiary'),
            new VisibleCSSLocator('emptyVariantsTableInfo', '.ibexa-table__empty-table-info-text'),
        ];
    }
}
