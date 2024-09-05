<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\ProductCatalog\Behat\Page\AttributeGroupPage;
use Ibexa\ProductCatalog\Behat\Page\AttributeGroupsPage;
use PHPUnit\Framework\Assert;

final class AttributeGroupsContext implements Context
{
    private AttributeGroupsPage $attributeGroupsPage;

    private AttributeGroupPage $attributeGroupPage;

    public function __construct(AttributeGroupsPage $attributeGroupsPage, AttributeGroupPage $attributeGroupPage)
    {
        $this->attributeGroupsPage = $attributeGroupsPage;
        $this->attributeGroupPage = $attributeGroupPage;
    }

    /**
     * @Then I should see an Attribute group with values
     */
    public function iVerifyAttributeGroup(TableNode $table): void
    {
        $this->attributeGroupPage->setExpectedAttributeGroupName($table->getHash()[0]['Name']);
        $this->attributeGroupPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->attributeGroupPage->verifyAttributeGroup($row['Name'], $row['Identifier'], $row['Position']);
        }
    }

    /**
     * @Then there is an Attribute group :attributeGroupName
     */
    public function iConfirmThatAttributeGroupContains(string $attributeName): void
    {
        $this->attributeGroupsPage->verifyIsLoaded();
        Assert::assertTrue($this->attributeGroupsPage->attributeGroupExists($attributeName));
    }

    /**
     * @When I edit :attributeGroupName Attribute group
     */
    public function iEditAttributeGroup(string $attributeGroupName): void
    {
        $this->attributeGroupsPage->editAttributeGroup($attributeGroupName);
    }

    /**
     * @When I delete the Attribute group
     */
    public function iDeleteAttributeGroup(): void
    {
        $this->attributeGroupPage->deleteAttributeGroup();
    }

    /**
     * @Then I open :attributeGroupName Attribute group page in admin SiteAccess
     */
    public function iOpenAttributeGroupPage(string $attributeGroupName): void
    {
        $this->attributeGroupPage->setExpectedAttributeGroupName($attributeGroupName);
        $this->attributeGroupPage->open('admin');
        $this->attributeGroupPage->verifyIsLoaded();
    }
}
