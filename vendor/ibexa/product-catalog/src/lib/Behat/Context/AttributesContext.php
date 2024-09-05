<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\ProductCatalog\Behat\Page\AttributeEditPage;
use Ibexa\ProductCatalog\Behat\Page\AttributePage;
use Ibexa\ProductCatalog\Behat\Page\AttributesPage;
use PHPUnit\Framework\Assert;

final class AttributesContext implements Context
{
    private AttributesPage $attributesPage;

    private AttributePage $attributePage;

    private AttributeEditPage $attributeEditPage;

    public function __construct(
        AttributesPage $attributesPage,
        AttributePage $attributePage,
        AttributeEditPage $attributeEditPage
    ) {
        $this->attributesPage = $attributesPage;
        $this->attributePage = $attributePage;
        $this->attributeEditPage = $attributeEditPage;
    }

    /**
     * @When I select the :attributeGroupName Attribute Group for the Attribute
     */
    public function iSelectAttributeGroupForAttribute(string $attributeGroupName): void
    {
        $this->attributeEditPage->selectAttributeGroup($attributeGroupName);
    }

    /**
     * @When I select :attributeType Attribute Type
     */
    public function iSelectAttributeType(string $attributeType): void
    {
        $this->attributesPage->selectAttributeOptions($attributeType);
    }

    /**
     * @Then I should see an Attribute with values
     */
    public function iVerifyAttribute(TableNode $table): void
    {
        $this->attributePage->setExpectedAttributeName($table->getHash()[0]['Name']);
        $this->attributePage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->attributePage->verifyAttribute($row['Name'], $row['Identifier'], $row['Description'], $row['Type'], $row['Group'], $row['Position']);
        }
    }

    /**
     * @Then there is an Attribute :attributeName
     */
    public function iConfirmThatAttributeContains(string $attributeName): void
    {
        $this->attributesPage->verifyIsLoaded();
        Assert::assertTrue($this->attributesPage->attributeExists($attributeName));
    }

    /**
     * @When I edit :attributeName Attribute
     */
    public function iEditAttribute(string $attributeName): void
    {
        $this->attributesPage->editAttribute($attributeName);
    }

    /**
     * @When I delete the Attribute
     */
    public function iDeleteAttribute(): void
    {
        $this->attributePage->deleteAttribute();
    }

    /**
     * @Then I open :attributeName Attribute page in admin SiteAccess
     */
    public function iOpenAttributePage(string $attributeName): void
    {
        $this->attributePage->setExpectedAttributeName($attributeName);
        $this->attributePage->open('admin');
        $this->attributePage->verifyIsLoaded();
    }
}
