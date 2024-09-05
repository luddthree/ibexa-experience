<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;

final class CompaniesPage extends Page
{
    private TableInterface $table;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder)
    {
        parent::__construct($session, $router);
        $this->table = $tableBuilder->newTable()->build();
    }

    public function companyWithStatusExists(string $companyName, string $companyStatus): bool
    {
        return $this->table->hasElement(['Name' => $companyName, 'Status' => $companyStatus]);
    }

    public function editCompany(string $companyName): void
    {
        $this->table->getTableRow(['Name' => $companyName])->edit();
    }

    public function deactivateCompany(string $companyName): void
    {
        $this->table->getTableRow(['Name' => $companyName])->deactivate();
    }

    public function activateCompany(string $companyName): void
    {
        $this->table->getTableRow(['Name' => $companyName])->activate();
    }

    public function viewCompany(string $companyName): void
    {
        $this->table->getTableRow(['Name' => $companyName])->goToItem();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('companiesPageHeader'))->assert()->textEquals('Companies');
    }

    public function getName(): string
    {
        return 'Companies';
    }

    protected function getRoute(): string
    {
        return '/company/list';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('companiesPageHeader', '.ibexa-page-title__title'),
        ];
    }
}
