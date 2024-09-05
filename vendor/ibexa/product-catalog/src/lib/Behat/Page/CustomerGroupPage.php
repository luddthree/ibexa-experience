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
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupQuery;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\Query\Criterion\CustomerGroupName;
use Ibexa\ProductCatalog\Local\Repository\CustomerGroupService;

final class CustomerGroupPage extends Page
{
    private string $expectedCustomerGroupName;

    private int $expectedCustomerGroupId;

    private Dialog $dialog;

    private CustomerGroupService $customerGroupService;

    public function __construct(Session $session, Router $router, Dialog $dialog, CustomerGroupService $customerGroupService)
    {
        parent::__construct($session, $router);
        $this->customerGroupService = $customerGroupService;
        $this->dialog = $dialog;
    }

    public function verifyCustomerGroup(string $customerGroupName, string $customerGroupIdentifier, string $customerGroupGlobalPriceRate): void
    {
        $this->getHTMLPage()->find($this->getLocator('customerGroupInformationName'))->assert()->textEquals($customerGroupName);
        $this->getHTMLPage()->find($this->getLocator('customerGroupInformationIdentifier'))->assert()->textEquals($customerGroupIdentifier);
        $this->getHTMLPage()->find($this->getLocator('customerGroupInformationGlobalPriceRate'))->assert()->textEquals($customerGroupGlobalPriceRate);
    }

    public function deleteCustomerGroup(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('deleteButton'))->click();
        $this->dialog->verifyIsLoaded();
        $this->dialog->confirm();
    }

    public function setExpectedCustomerGroupName(string $customerGroupName): void
    {
        $this->expectedCustomerGroupName = $customerGroupName;
        $query = new CustomerGroupQuery(new CustomerGroupName($this->expectedCustomerGroupName));
        $customerGroup = $this->customerGroupService->findCustomerGroups($query)->getCustomerGroups()[0];
        $this->expectedCustomerGroupId = $customerGroup->getId();
    }

    public function setGlobalPriceRate(string $globalRateValue): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('globalPriceRateInput'))->setValue($globalRateValue);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('customerGroupPageHeader'))->assert()->textContains($this->expectedCustomerGroupName);
    }

    public function getName(): string
    {
        return 'Customer Group';
    }

    protected function getRoute(): string
    {
        return sprintf(
            '/customer-group/%d',
            $this->expectedCustomerGroupId
        );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('customerGroupPageHeader', '.ibexa-page-title__title'),
            new VisibleCSSLocator('deleteButton', '.ibexa-btn--secondary'),
            new VisibleCSSLocator('customerGroupInformationName', 'div.ibexa-details__item:nth-of-type(1) .ibexa-details__item-content'),
            new VisibleCSSLocator('customerGroupInformationIdentifier', 'div.ibexa-details__item:nth-of-type(2) .ibexa-details__item-content'),
            new VisibleCSSLocator('customerGroupInformationGlobalPriceRate', 'div.ibexa-details__item:nth-of-type(4) .ibexa-details__item-content'),
            new VisibleCSSLocator('globalPriceRateInput', '#customer_group_create_global_price_rate, #customer_group_update_global_price_rate'),
        ];
    }
}
