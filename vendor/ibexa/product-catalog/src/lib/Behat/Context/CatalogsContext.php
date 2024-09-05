<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\Contracts\ProductCatalog\CatalogServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Catalog\CatalogCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductType;
use Ibexa\ProductCatalog\Behat\Page\CatalogPage;
use Ibexa\ProductCatalog\Behat\Page\CatalogsPage;
use Ibexa\ProductCatalog\Behat\Page\CreateCatalogPage;
use Ibexa\ProductCatalog\Local\Repository\Values\Catalog\Status;
use PHPUnit\Framework\Assert;

final class CatalogsContext implements Context
{
    private CatalogsPage $catalogsPage;

    private CatalogPage $catalogPage;

    private CreateCatalogPage $createCatalogPage;

    private CatalogServiceInterface $catalogService;

    public function __construct(CatalogsPage $catalogsPage, CatalogPage $catalogPage, CreateCatalogPage $createCatalogPage, CatalogServiceInterface $catalogService)
    {
        $this->catalogsPage = $catalogsPage;
        $this->catalogPage = $catalogPage;
        $this->createCatalogPage = $createCatalogPage;
        $this->catalogService = $catalogService;
    }

    /**
     * @Given I create :catalogName Catalog with :catalogIdentifier Identifier for :productTypeIdentifierFirst and :productTypeIdentifierSecond Products
     */
    public function createCatalog(string $catalogName, string $catalogIdentifier, string $productTypeIdentifierFirst, string $productTypeIdentifierSecond): void
    {
        $criterion = new ProductType([$productTypeIdentifierFirst, $productTypeIdentifierSecond]);

        $catalogCreateStruct = new CatalogCreateStruct(
            $catalogIdentifier,
            $criterion,
            ['eng-GB' => $catalogName],
            ['eng-GB' => $catalogName],
            Status::PUBLISHED_PLACE,
            14 // Admin User ID
        );
        $this->catalogService->createCatalog($catalogCreateStruct);
    }

    /**
     * @Then there is a Catalog :catalogName
     */
    public function iConfirmThatCatalogContains(string $catalogName): void
    {
        $this->catalogsPage->verifyIsLoaded();
        Assert::assertTrue($this->catalogsPage->catalogExists($catalogName));
    }

    /**
     * @When I publish the Catalog from Catalog page
     */
    public function iPublishCatalogFromCatalogPage(): void
    {
        $this->catalogPage->publishCatalog();
    }

    /**
     * @When I archive the Catalog from Catalog page
     */
    public function iArchiveCatalogFromCatalogPage(): void
    {
        $this->catalogPage->archiveCatalog();
    }

    /**
     * @When I copy the Catalog from Catalog page
     */
    public function iCopyCatalogFromCatalogPage(): void
    {
        $this->catalogPage->copyCatalog();
    }

    /**
     * @When I delete the Catalog from Catalog page
     */
    public function iDeleteCatalogFromCatalogPage(): void
    {
        $this->catalogPage->deleteCatalog();
    }

    /**
     * @When I edit :catalogName Catalog
     */
    public function iEditCatalog(string $catalogName): void
    {
        $this->catalogsPage->editCatalog($catalogName);
    }

    /**
     * @When I copy :catalogName Catalog
     */
    public function iCopyCatalog(string $catalogName): void
    {
        $this->catalogsPage->copyCatalog($catalogName);
    }

    /**
     * @When I delete the Catalog :catalogName
     */
    public function iDeleteCatalog(string $catalogName): void
    {
        $this->catalogsPage->deleteCatalog($catalogName);
    }

    /**
     * @Then I should see a Catalog with values
     */
    public function iVerifyCatalog(TableNode $table): void
    {
        $this->catalogPage->setExpectedCatalogName($table->getHash()[0]['Name']);
        $this->catalogPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->catalogPage->verifyCatalog($row['Name'], $row['Identifier']);
        }
    }

    /**
     * @Then catalog status is :catalogStatus
     */
    public function iVerifyCatalogStatus(string $catalogStatus): void
    {
        $this->catalogPage->verifyIsLoaded();
        $this->catalogPage->verifyCatalogStatus($catalogStatus);
    }

    /**
     * @Then there's no :catalogName on Catalogs list
     */
    public function thereIsNoCatalogOnList(string $catalogName): void
    {
        Assert::assertFalse($this->catalogsPage->catalogExists($catalogName));
    }

    /**
     * @Then I should see products on catalog form
     */
    public function iVerifyCatalogProductsAreDisplayed(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $this->createCatalogPage->verifyProductIsDisplayedInCatalog($row['Name'], $row['Code']);
        }
    }

    /**
     * @Then I should not see products on catalog form
     */
    public function iVerifyCatalogProductsAreNotDisplayed(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            $this->createCatalogPage->verifyProductIsNotDisplayedInCatalog($row['Name'], $row['Code']);
        }
    }

    /**
     * @Then I add filter :filterType to catalog
     */
    public function iAddFilter(string $filterType): void
    {
        $this->createCatalogPage->verifyIsLoaded();
        $this->createCatalogPage->addFilter($filterType);
    }

    /**
     * @Then I set filter range parameters to :inputFrom and :inputTo
     */
    public function iSetFieldParameters(string $inputFrom, string $inputTo): void
    {
        $this->createCatalogPage->setFieldRangeParameters($inputFrom, $inputTo);
    }

    /**
     * @Then I select :option option in product type filter
     */
    public function iSelectFilterProductTypeOption(string $option): void
    {
        $this->createCatalogPage->selectFilterRadioOption($option);
    }

    /**
     * @Then I fill in Product code with :codeValue value
     */
    public function iFillInProductCodeValue(string $codeValue): void
    {
        $this->createCatalogPage->setFilterValue($codeValue);
    }

    /**
     * @Then I save editing the filter
     */
    public function iClickOnSaveButtonInFilterForm(): void
    {
        $this->createCatalogPage->saveEditingFilter();
    }

    /**
     * @Then I cancel editing the filter
     */
    public function iClickOnCancelButtonInFilterForm(): void
    {
        $this->createCatalogPage->cancelEditingFilter();
    }

    /**
     * @Then I edit :filterName filter from filters list
     */
    public function iEditFilter(string $filterName): void
    {
        $this->createCatalogPage->editFilter($filterName);
    }

    /**
     * @Then I delete :filterName filter from filters list
     */
    public function iDeleteFilter(string $filterName): void
    {
        $this->createCatalogPage->verifyIsLoaded();
        $this->createCatalogPage->deleteFilter($filterName);
    }

    /**
     * @Then there's no :filterName filter on filters list
     */
    public function iVerifyFilterVisibilityOnFilterList(string $filterName): void
    {
        $this->createCatalogPage->verifyIsLoaded();
        Assert::assertNotContains($filterName, $this->createCatalogPage->getActiveFilters());
    }

    /**
     * @Then there is :filterType filter on filters list
     * @Then there is :filterType filter with value :filterValue on filters list
     */
    public function iVerifyFilterAvailabilityOnFilterList(string $filterType, string $filterValue = null): void
    {
        $this->createCatalogPage->verifyFilterAvailability($filterType, $filterValue);
    }

    /**
     * @Then I open :catalogName Catalog page in admin SiteAccess
     */
    public function iOpenCatalogPage(string $catalogName): void
    {
        $this->catalogPage->setExpectedCatalogName($catalogName);
        $this->catalogPage->open('admin');
        $this->catalogPage->verifyIsLoaded();
    }
}
