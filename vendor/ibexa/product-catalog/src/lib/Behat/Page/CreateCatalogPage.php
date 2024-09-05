<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Element\Mapper\ElementTextMapper;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\ProductCatalog\Behat\Component\NewFilterDefinitionSideMenu;
use PHPUnit\Framework\Assert;

final class CreateCatalogPage extends Page
{
    private IbexaDropdown $ibexaDropdown;

    private NewFilterDefinitionSideMenu $newFilterDefinitionSideMenu;

    private TableInterface $table;

    public function __construct(
        Session $session,
        Router $router,
        IbexaDropdown $ibexaDropdown,
        NewFilterDefinitionSideMenu $newFilterDefinitionSideMenu,
        TableBuilder $tableBuilder
    ) {
        parent::__construct($session, $router);
        $this->ibexaDropdown = $ibexaDropdown;
        $this->newFilterDefinitionSideMenu = $newFilterDefinitionSideMenu;
        $this->table = $tableBuilder->newTable()->build();
    }

    public function verifyProductIsDisplayedInCatalog(string $expectedProductName, string $expectedProductCode): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('loadingTable')));
        Assert::assertTrue($this->table->hasElement(['Name' => $expectedProductName, 'Code' => $expectedProductCode]));
    }

    public function verifyProductIsNotDisplayedInCatalog(string $expectedProductName, string $expectedProductCode): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('loadingTable')));
        Assert::assertFalse($this->table->hasElement(['Name' => $expectedProductName, 'Code' => $expectedProductCode]));
    }

    public function addFilter(string $filterType): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('addFilterButton'))
            ->assert()->textEquals('Add filter')
            ->click();

        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($filterType);
        $this->newFilterDefinitionSideMenu->verifyIsLoaded();
    }

    public function setFieldRangeParameters(string $inputFrom, string $inputTo): void
    {
        $this->newFilterDefinitionSideMenu->verifyIsLoaded();
        $this->newFilterDefinitionSideMenu->setFilterValues($inputFrom, $inputTo);
        $this->newFilterDefinitionSideMenu->confirm();
    }

    public function setFilterValue(string $codeValue): void
    {
        $this->newFilterDefinitionSideMenu->verifyIsLoaded();
        $this->newFilterDefinitionSideMenu->setProductCodeValue($codeValue);
        $this->newFilterDefinitionSideMenu->confirm();
    }

    public function saveEditingFilter(): void
    {
        $this->newFilterDefinitionSideMenu->verifyIsLoaded();
        $this->newFilterDefinitionSideMenu->confirm();
    }

    public function cancelEditingFilter(): void
    {
        $this->newFilterDefinitionSideMenu->verifyIsLoaded();
        $this->newFilterDefinitionSideMenu->decline();
    }

    /**
     * @return string[]
     */
    public function getActiveFilters(): array
    {
        return $this->getHTMLPage()
            ->setTimeout(5)
            ->findAll($this->getLocator('filterName'))
            ->mapBy(new ElementTextMapper());
    }

    public function verifyFilterAvailability(string $expectedFilterName, string $expectedFilterValue = null): void
    {
        $filter = $this->getHTMLPage()
            ->setTimeout(5)
            ->findAll($this->getLocator('activeFilters'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('filterName'), $expectedFilterName));

        $filter->find($this->getLocator('filterName'))->assert()->textEquals($expectedFilterName);

        if ($expectedFilterValue !== null) {
            $filter->find($this->getLocator('filterValue'))->assert()->textEquals($expectedFilterValue);
        }
    }

    public function selectFilterRadioOption(string $option): void
    {
        $this->newFilterDefinitionSideMenu->verifyIsLoaded();
        $this->newFilterDefinitionSideMenu->selectProductAvailabilityOption($option);
    }

    public function editFilter(string $filterName): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('activeFilters'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('filterName'), $filterName))
            ->find($this->getLocator('editFilterButton'))
            ->click();
    }

    public function deleteFilter(string $filterName): void
    {
        $this->getHTMLPage()->setTimeout(5)->findAll($this->getLocator('activeFilters'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('filterName'), $filterName))
            ->find($this->getLocator('deleteFilterButton'))
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->setTimeout(5)->find($this->getLocator('createCatalogPageHeader'))->assert()->textContains('New Catalog');
    }

    public function getName(): string
    {
        return 'New Catalog';
    }

    protected function getRoute(): string
    {
        return '/catalog/create';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createCatalogPageHeader', '.ibexa-edit-header__title'),
            new VisibleCSSLocator('loadingTable', '.ibexa-table__empty-table-cell .ibexa-spin'),
            new VisibleCSSLocator('addFilterButton', '.ibexa-pc-edit-catalog-filters__available-popup-trigger'),
            new VisibleCSSLocator('activeFilters', '.ibexa-pc-edit-catalog-filter-preview'),
            new VisibleCSSLocator('filterName', '.ibexa-pc-edit-catalog-filter-preview__name'),
            new VisibleCSSLocator('filterValue', '.ibexa-pc-edit-catalog-filter-preview__value'),
            new VisibleCSSLocator('editFilterButton', '.ibexa-pc-edit-catalog-filter-preview__open-config-panel'),
            new VisibleCSSLocator('deleteFilterButton', '.ibexa-pc-edit-catalog-filter-preview__remove-filter-preview'),
        ];
    }
}
