<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\ProductCatalog\Behat\Page\ProductTypePage;
use Ibexa\ProductCatalog\Behat\Page\ProductTypesPage;
use Ibexa\ProductCatalog\Behat\Page\ProductTypeUpdatePage;
use PHPUnit\Framework\Assert;

final class ProductTypesContext implements Context
{
    private ProductTypesPage $productTypesPage;

    private ProductTypePage $productTypePage;

    private ProductTypeUpdatePage $productTypeUpdatePage;

    private ContentActionsMenu $contentActionsMenu;

    public function __construct(ProductTypesPage $productTypesPage, ProductTypePage $productTypePage, ProductTypeUpdatePage $productTypeUpdatePage, ContentActionsMenu $contentActionsMenu)
    {
        $this->productTypesPage = $productTypesPage;
        $this->productTypePage = $productTypePage;
        $this->productTypeUpdatePage = $productTypeUpdatePage;
        $this->contentActionsMenu = $contentActionsMenu;
    }

    /**
     * @Given there's no :productTypeName on Product Types list
     */
    public function thereIsNoOnProductTypesList(string $productTypeName): void
    {
        Assert::assertFalse($this->productTypesPage->isProductTypeOnTheList($productTypeName));
    }

    /**
     * @Given there's a :productTypeName on Product Types list
     */
    public function thereIsOnProductTypesList(string $productTypeName): void
    {
        Assert::assertTrue($this->productTypesPage->isProductTypeOnTheList($productTypeName));
    }

    /**
     * @Given I should be on Product Type page for :productTypeName
     */
    public function iShouldBeOnProductTypePage(string $productTypeName): void
    {
        $this->productTypePage->setExpectedProductTypeName($productTypeName);
        $this->productTypePage->verifyIsLoaded();
    }

    /**
     * @When I start editing Product Type :productTypeName
     */
    public function iStartEditingItem(string $productTypeName): void
    {
        $this->productTypesPage->edit($productTypeName);
    }

    /**
     * @When I delete :productTypeName Product Type
     */
    public function iDeleteProductType(string $productTypeName): void
    {
        $this->productTypesPage->delete($productTypeName);
    }

    /**
     * @Then Product Type has proper Global properties
     */
    public function productTypeHasProperGlobalProperties(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            Assert::assertTrue($this->productTypePage->hasProperty($row['label'], $row['value']));
        }
    }

    /**
     * @Then Product Type :productTypeName has proper fields
     */
    public function productTypeHasProperFields(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            Assert::assertTrue($this->productTypePage->hasFieldType(
                ['Name' => $row['fieldName'], 'Type' => $row['fieldType']]
            ));
        }
    }

    /**
     * @Then I add attribute group :attributeGroupName to Product Type definition
     */
    public function iAddAttributeGroup(string $attributeGroupName): void
    {
        $this->productTypeUpdatePage->addAttributeGroup($attributeGroupName);
    }

    /**
     * @Then I add attribute :attributeName to Product Type definition
     */
    public function iAddAttribute(string $attributeName): void
    {
        $this->productTypeUpdatePage->addAttribute($attributeName);
    }

    /**
     * @When I add field :fieldName to Product Type definition
     */
    public function iAddField(string $fieldName): void
    {
        $this->productTypeUpdatePage->addFieldDefinition($fieldName);
    }

    /**
     * @When I set :field to :value for :fieldName Product Type field
     */
    public function iSetProductTypeFieldDefinitionData(string $label, string $value, string $fieldName): void
    {
        $this->productTypeUpdatePage->fillFieldDefinitionFieldWithValue($fieldName, $label, $value);
    }

    /**
     * @When I start creating a :typeOfProductType Product Type
     */
    public function iselectTypeOfProductType(string $typeOfProductType): void
    {
        $this->contentActionsMenu->clickButton('Create');
        $this->productTypesPage->selectTypeOfProductType($typeOfProductType);
    }

    /**
     * @Then I set Required field for an attribute type :attributeName to enabled in Product Type field definition
     */
    public function iSetRequiredToggleButtonEnabled(string $attributeType): void
    {
        $this->productTypeUpdatePage->setFieldRequired($attributeType);
    }

    /**
     * @Then I set Used for product variants field for an attribute type :attributeName to enabled in Product Type field definition
     */
    public function iSetUsedForProductVariantsToggleButtonEnabled(string $attributeType): void
    {
        $this->productTypeUpdatePage->setFieldUsedForProductVariants($attributeType);
    }

    /**
     * @Then Product Type :productTypeName has proper attributes
     */
    public function productTypeHasProperAttributes(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            Assert::assertTrue($this->productTypePage->hasAttribute(
                ['Identifier' => $row['attributeIdentifier'], 'Name' => $row['attributeName']]
            ));
        }
    }

    /**
     * @Then Product Type :productTypeName has proper VAT Rates
     */
    public function productTypeHasProperVATRates(TableNode $table): void
    {
        foreach ($table->getHash() as $row) {
            Assert::assertTrue($this->productTypePage->hasVATRates(
                ['Region' => $row['vatRegion'], 'Identifier' => $row['vatIdentifier'], 'Value' => $row['vatValue']]
            ));
        }
    }

    /**
     * @Then I set VAT rate for :regionName region to :vatValue
     */
    public function iSetVATRate(string $regionName, string $vatValue): void
    {
        $this->productTypeUpdatePage->setVATRate($regionName, $vatValue);
    }
}
