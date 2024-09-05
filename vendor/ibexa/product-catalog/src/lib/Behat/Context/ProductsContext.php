<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Page\ContentViewPage;
use Ibexa\ProductCatalog\Behat\Component\CategoryFilter;
use Ibexa\ProductCatalog\Behat\Component\GenerateProductVariantsPopupWindow;
use Ibexa\ProductCatalog\Behat\Component\ProductAvailabilityTab;
use Ibexa\ProductCatalog\Behat\Component\ProductPriceTab;
use Ibexa\ProductCatalog\Behat\Component\ProductVariantTab;
use Ibexa\ProductCatalog\Behat\Page\ProductAvailabilityUpdatePage;
use Ibexa\ProductCatalog\Behat\Page\ProductCreatePage;
use Ibexa\ProductCatalog\Behat\Page\ProductPage;
use Ibexa\ProductCatalog\Behat\Page\ProductPriceUpdatePage;
use Ibexa\ProductCatalog\Behat\Page\ProductsPage;
use Ibexa\ProductCatalog\Behat\Page\ProductVariantUpdatePage;
use PHPUnit\Framework\Assert;

final class ProductsContext implements Context
{
    private ProductsPage $productsPage;

    private ProductPage $productPage;

    private ProductPriceUpdatePage $productPriceUpdatePage;

    private ProductPriceTab $productPriceTab;

    private ProductAvailabilityUpdatePage $productAvailabilityUpdatePage;

    private ProductAvailabilityTab $productAvailabilityTab;

    private ProductCreatePage $productCreatePage;

    private ContentViewPage $contentViewPage;

    private CategoryFilter $categoryFilter;

    private ProductVariantUpdatePage $productVariantUpdatePage;

    private ProductVariantTab $productVariantTab;

    private GenerateProductVariantsPopupWindow $generateProductVariantsPopupWindow;

    public function __construct(
        ProductsPage $productsPage,
        ProductPage $productPage,
        ProductPriceUpdatePage $productPriceUpdatePage,
        ProductPriceTab $productPriceTab,
        ProductAvailabilityUpdatePage $productAvailabilityUpdatePage,
        ProductAvailabilityTab $productAvailabilityTab,
        ProductCreatePage $productCreatePage,
        ContentViewPage $contentViewPage,
        CategoryFilter $categoryFilter,
        ProductVariantUpdatePage $productVariantUpdatePage,
        ProductVariantTab $productVariantTab,
        GenerateProductVariantsPopupWindow $generateProductVariantsPopupWindow
    ) {
        $this->productsPage = $productsPage;
        $this->productPage = $productPage;
        $this->productPriceUpdatePage = $productPriceUpdatePage;
        $this->productPriceTab = $productPriceTab;
        $this->productAvailabilityUpdatePage = $productAvailabilityUpdatePage;
        $this->productAvailabilityTab = $productAvailabilityTab;
        $this->productCreatePage = $productCreatePage;
        $this->contentViewPage = $contentViewPage;
        $this->categoryFilter = $categoryFilter;
        $this->productVariantUpdatePage = $productVariantUpdatePage;
        $this->productVariantTab = $productVariantTab;
        $this->generateProductVariantsPopupWindow = $generateProductVariantsPopupWindow;
    }

    /**
     * @When I select :productType Product Type
     */
    public function iSelectProductType(string $productType): void
    {
        $this->productsPage->selectProductOptions($productType);
    }

    /**
     * @Given there's no :productName on Products list
     */
    public function thereIsNoOnProductsList(string $productName): void
    {
        Assert::assertFalse($this->productsPage->isProductTypeOnTheList($productName));
    }

    /**
     * @Given there's a :productName on Products list
     */
    public function thereIsOnProductsList(string $productName): void
    {
        Assert::assertTrue($this->productsPage->isProductTypeOnTheList($productName));
    }

    /**
     * @Then I should be on Product page for :productName
     */
    public function iShouldBeOnProductPageFor(string $productName): void
    {
        $this->productPage->setExpectedProductName($productName);
        $this->productPage->verifyIsLoaded();
    }

    /**
     * @When I start editing Product :productName
     */
    public function iStartEditingItem(string $productName): void
    {
        $this->productsPage->edit($productName);
    }

    /**
     * @When I delete :productName Product
     */
    public function iDeleteProduct(string $productName): void
    {
        $this->productsPage->delete($productName);
    }

    /**
     * @When I confirm deletion of product from product page
     */
    public function iDeleteProductFromProductPage(): void
    {
        $this->productVariantTab->delete();
    }

    /**
     * @Then product attributes equal
     */
    public function productAttributesEqual(TableNode $parameters): void
    {
        foreach ($parameters->getHash() as $fieldData) {
            $fieldLabel = $fieldData['label'];
            $type = $fieldData['type'];
            $expectedFieldValues = $fieldData;
            $this->productPage->verifyFieldHasValues($fieldLabel, $expectedFieldValues, $type);
        }
    }

    /**
     * @When I change tab to :tabName
     */
    public function switchTab(string $tabName): void
    {
        $this->contentViewPage->switchToTab($tabName);
    }

    /**
     * @Then I open :productName Product page in admin SiteAccess
     */
    public function iOpenProductPage(string $productName): void
    {
        $this->productPage->setExpectedProductName($productName);
        $this->productPage->open('admin');
        $this->productPage->verifyIsLoaded();
    }

    /**
     * @Then I open :productVariantName Product variant page in admin SiteAccess
     */
    public function iOpenProductVariantPage(string $productVariantCode): void
    {
        $this->productPage->setExpectedProductVariantCode($productVariantCode);
        $this->productPage->open('admin');
        $this->productPage->verifyIsLoaded();
    }

    /**
     * @Then I set a Base price to :value in :currency
     */
    public function iSetBasePrice(int $value, string $currency): void
    {
        $this->productPriceTab->selectCurrency($currency);
        $this->productPriceTab->addPrice();
        $this->productPriceUpdatePage->setBasePrice($value);
    }

    /**
     * @Then I should see a Base price with :value value
     */
    public function iVerifyBasePrice(string $value): void
    {
        $this->productPriceTab->verifyBasePrice($value);
    }

    /**
     * @Then I start adding availability to product
     */
    public function iAddAvailability(): void
    {
        $this->productAvailabilityTab->addAvailability();
    }

    /**
     * @Then I set a Stock to :value
     */
    public function iSetStock(int $value): void
    {
        $this->productAvailabilityUpdatePage->setStock($value);
    }

    /**
     * @Then I set an Availability to :value
     */
    public function iSetAvailability(string $value): void
    {
        $shouldBeAvailable = $value === 'true';
        $this->productAvailabilityUpdatePage->setAvailability($shouldBeAvailable);
    }

    /**
     * @Then I should see a Stock with :value value
     */
    public function iVerifyStock(int $value): void
    {
        $this->productAvailabilityTab->verifyStock($value);
    }

    /**
     * @Then I should see an Availability with :value value
     */
    public function iVerifyAvailability(string $value): void
    {
        $shouldBeAvailable = $value === 'true';
        $this->productAvailabilityTab->verifyAvailability($shouldBeAvailable);
    }

    /**
     * @Then I click on Generate variants button
     */
    public function iClickOnGenerateVariantsButton(): void
    {
        $this->productVariantTab->clickOnGenerateVariantsButton();
    }

    /**
     * @Then I confirm generating Product Variants
     */
    public function iConfirmGeneratingProductVariants(): void
    {
        $this->generateProductVariantsPopupWindow->confirmGeneratingProductVariants();
    }

    /**
     * @Then I cancel generating Product Variants
     */
    public function iCancelGeneratingProductVariants(): void
    {
        $this->generateProductVariantsPopupWindow->cancelGeneratingProductVariants();
    }

    /**
     * @Given there's a Product Variant :productVariantName on Variants list with Product code :productCode
     */
    public function iVerifyThatProductVariantIsOnList(string $productVariantName, string $productCode): void
    {
        Assert::assertTrue($this->productVariantTab->isProductVariantOnTheList($productVariantName, $productCode));
    }

    /**
     * @Given there's no :productVariantName on Variants list with Product code :productCode
     */
    public function iVerifyThatProductVariantIsNotOnList(string $productVariantName, string $productCode): void
    {
        Assert::assertFalse($this->productVariantTab->isProductVariantOnTheList($productVariantName, $productCode));
    }

    /**
     * @When I click on add Product Variant button
     */
    public function iAddProductVariant(): void
    {
        $this->productVariantTab->addVariant();
    }

    /**
     * @When I delete :productVariantName Product Variant
     */
    public function iDeleteProductVariant(string $productVariantName): void
    {
        $this->productVariantTab->deleteVariant($productVariantName);
    }

    /**
     * @When I start editing Product Variant :productVariantName
     */
    public function iStartEditingProductVariant(string $productVariantName): void
    {
        $this->productVariantTab->edit($productVariantName);
    }

    /**
     * @When variants list is empty
     */
    public function iVerifyEmptyVariantsList(): void
    {
        Assert::assertEquals('There are no variants yet', $this->productVariantTab->getEmptyTableInformation());
    }

    /**
     * @Given there's a Product :productName on Products list with Product code :productCode and Variant status :variantStatus
     */
    public function iVerifyProductVariantStatus(string $productName, string $productCode, string $variantStatus): void
    {
        Assert::assertTrue($this->productsPage->isProductWithVariantsOnList($productName, $productCode, $variantStatus));
    }

    /**
     * @Then I set product attributes
     */
    public function iSetAttributes(TableNode $table): void
    {
        $this->productCreatePage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $row;
            $this->productCreatePage->fillAttributeWithValue($row['label'], $values, $row['attributeType']);
        }
    }

    /**
     * @Then I set product variant attributes in input fields on generate popup window
     */
    public function iSetProductVariantAttributesInputValues(TableNode $table): void
    {
        $this->generateProductVariantsPopupWindow->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $row;
            $this->generateProductVariantsPopupWindow->fillProductVariantAttributeInputValues($row['label'], $values);
        }
    }

    /**
     * @Then I set product variant attributes in dropdown fields on generate popup window
     */
    public function iSetProductVariantAttributesDropdownValues(TableNode $table): void
    {
        $this->generateProductVariantsPopupWindow->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $row;
            $this->generateProductVariantsPopupWindow->selectProductVariantAttributeDropdownValues($row['label'], $values);
        }
    }

    /**
     * @Then I set product variant attributes values in input fields on update page
     */
    public function iSetProductVariantAttributesInputValuesOnUpdatePage(TableNode $table): void
    {
        $this->productVariantUpdatePage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $row;
            $this->productVariantUpdatePage->fillProductVariantInputFields($row['label'], $values);
        }
    }

    /**
     * @Then I set product variant attributes values in dropdown fields on update page
     */
    public function iSetProductVariantAttributesDropdownValuesOnUpdatePage(TableNode $table): void
    {
        $this->productVariantUpdatePage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $values = $row;
            $this->productVariantUpdatePage->selectProductVariantDropdownFields($row['label'], $values);
        }
    }

    /**
     * @Given I change category filter to :filterName
     */
    public function iChangeCategoryFilter(string $filterName): void
    {
        $this->categoryFilter->verifyIsLoaded();
        $this->categoryFilter->changeCategoryFilter($filterName);
    }
}
