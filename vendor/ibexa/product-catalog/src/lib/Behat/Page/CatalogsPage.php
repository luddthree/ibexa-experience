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

final class CatalogsPage extends Page
{
    private TableInterface $table;

    private Dialog $dialog;

    public function __construct(Session $session, Router $router, Dialog $dialog, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->dialog = $dialog;
        $this->table = $tableBuilder->newTable()->build();
    }

    public function catalogExists(string $catalogName): bool
    {
        return $this->table->hasElement(['Name' => $catalogName]);
    }

    public function editCatalog(string $catalogName): void
    {
        $this->table->getTableRow(['Name' => $catalogName])->edit();
    }

    public function copyCatalog(string $catalogName): void
    {
        $this->table->getTableRow(['Name' => $catalogName])->copy();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function deleteCatalog(string $catalogName): void
    {
        $this->table->getTableRow(['Name' => $catalogName])->select();
        $this->getHTMLPage()->find($this->getLocator('tableDeleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('catalogsPageHeader'))->assert()->textEquals('Catalogs');
    }

    public function getName(): string
    {
        return 'Catalogs';
    }

    protected function getRoute(): string
    {
        return '/catalog';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('catalogsPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('tableDeleteButton', '.ibexa-pc-data-grid__delete-btn'),
        ];
    }
}
