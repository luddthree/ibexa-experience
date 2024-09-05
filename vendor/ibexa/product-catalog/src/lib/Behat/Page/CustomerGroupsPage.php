<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

final class CustomerGroupsPage extends Page
{
    private TableInterface $table;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
    }

    public function customerGroupExists(string $customerGroupName): bool
    {
        return $this->table->hasElement(['Name' => $customerGroupName]);
    }

    public function editCustomerGroup(string $customerGroupName): void
    {
        $this->table->getTableRow(['Name' => $customerGroupName])->edit();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('customerGroupsPageHeader'))->assert()->textEquals('Customer Groups');
    }

    public function getName(): string
    {
        return 'Customer Groups';
    }

    protected function getRoute(): string
    {
        return '/customer-group';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('customerGroupsPageHeader', '.ibexa-page-title__title'),
        ];
    }
}
