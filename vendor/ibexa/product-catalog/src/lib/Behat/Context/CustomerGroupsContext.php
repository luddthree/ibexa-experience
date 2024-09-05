<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\ProductCatalog\Behat\Page\CustomerGroupPage;
use Ibexa\ProductCatalog\Behat\Page\CustomerGroupsPage;
use PHPUnit\Framework\Assert;

final class CustomerGroupsContext implements Context
{
    private CustomerGroupsPage $customerGroupsPage;

    private CustomerGroupPage $customerGroupPage;

    public function __construct(CustomerGroupsPage $customerGroupsPage, CustomerGroupPage $customerGroupPage)
    {
        $this->customerGroupsPage = $customerGroupsPage;
        $this->customerGroupPage = $customerGroupPage;
    }

    /**
     * @Then I should see a Customer group with values
     */
    public function iVerifyCustomerGroup(TableNode $table): void
    {
        $this->customerGroupPage->setExpectedCustomerGroupName($table->getHash()[0]['Name']);
        $this->customerGroupPage->verifyIsLoaded();
        foreach ($table->getHash() as $row) {
            $this->customerGroupPage->verifyCustomerGroup($row['Name'], $row['Identifier'], $row['Global price rate']);
        }
    }

    /**
     * @Then there is a :customerGroupName Customer group
     */
    public function iConfirmThatCustomerGroupContains(string $customerName): void
    {
        $this->customerGroupsPage->verifyIsLoaded();
        Assert::assertTrue($this->customerGroupsPage->customerGroupExists($customerName));
    }

    /**
     * @When I edit :customerGroupName Customer group
     */
    public function iEditCustomerGroup(string $customerGroupName): void
    {
        $this->customerGroupsPage->editCustomerGroup($customerGroupName);
    }

    /**
     * @When I delete the Customer group
     */
    public function iDeleteCustomerGroup(): void
    {
        $this->customerGroupPage->deleteCustomerGroup();
    }

    /**
     * @Then I set global price rate to :value
     */
    public function iSetGlobalPriceRate(string $value): void
    {
        $this->customerGroupPage->setGlobalPriceRate($value);
    }

    /**
     * @Then I open :customerGroupName Customer group page in admin SiteAccess
     */
    public function iOpenCustomerGroupPage(string $customerGroupName): void
    {
        $this->customerGroupPage->setExpectedCustomerGroupName($customerGroupName);
        $this->customerGroupPage->open('admin');
        $this->customerGroupPage->verifyIsLoaded();
    }
}
