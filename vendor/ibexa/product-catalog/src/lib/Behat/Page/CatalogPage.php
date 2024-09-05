<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Dialog;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogQuery;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\Query\Criterion\CatalogName;
use Ibexa\ProductCatalog\Local\Repository\CatalogService;

final class CatalogPage extends Page
{
    private string $expectedCatalogName;

    private int $expectedCatalogId;

    private Dialog $dialog;

    private CatalogService $catalogService;

    public function __construct(Session $session, Router $router, Dialog $dialog, CatalogService $catalogService)
    {
        parent::__construct($session, $router);
        $this->dialog = $dialog;
        $this->catalogService = $catalogService;
    }

    public function verifyCatalog(string $catalogName, string $catalogIdentifier): void
    {
        $this->getHTMLPage()->find($this->getLocator('catalogInformationName'))->assert()->textEquals($catalogName);
        $this->getHTMLPage()->find($this->getLocator('catalogInformationIdentifier'))->assert()->textEquals($catalogIdentifier);
    }

    public function verifyCatalogStatus(string $catalogStatus): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('catalogStatus'))->assert()->textEquals($catalogStatus);
    }

    public function publishCatalog(): void
    {
        $this->getHTMLPage()->find($this->getLocator('publishButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function archiveCatalog(): void
    {
        $this->getHTMLPage()->find($this->getLocator('archiveButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function copyCatalog(): void
    {
        $this->getHTMLPage()->find($this->getLocator('copyButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function deleteCatalog(): void
    {
        $this->getHTMLPage()->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function setExpectedCatalogName(string $catalogName): void
    {
        $this->expectedCatalogName = $catalogName;
        $query = new CatalogQuery(new CatalogName($this->expectedCatalogName));
        $catalog = $this->catalogService->findCatalogs($query)->getCatalogs()[0];
        $this->expectedCatalogId = $catalog->getId();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('catalogPageHeader'))->assert()->textContains($this->expectedCatalogName);
    }

    public function getName(): string
    {
        return 'Catalog';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/catalog/%d',
            $this->expectedCatalogId
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('catalogPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('publishButton', '#catalog_view__context_menu__publish-tab'),
            new VisibleCSSLocator('archiveButton', '#catalog_view__context_menu__archive-tab'),
            new VisibleCSSLocator('copyButton', '#catalog_view__context_menu__copy-tab'),
            new VisibleCSSLocator('deleteButton', '#catalog_view__context_menu__delete-tab'),
            new VisibleCSSLocator('catalogStatus', '.ibexa-badge'),
            new VisibleCSSLocator('catalogInformationName', 'div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('catalogInformationIdentifier', 'div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
        ];
    }
}
