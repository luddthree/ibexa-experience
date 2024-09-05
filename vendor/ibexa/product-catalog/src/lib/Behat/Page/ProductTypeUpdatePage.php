<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\AdminUi\Behat\Page\ContentTypeUpdatePage;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Routing\Router;

final class ProductTypeUpdatePage extends ContentTypeUpdatePage
{
    private TableInterface $table;

    private TableInterface $vatTable;

    private IbexaDropdown $ibexaDropdown;

    public function __construct(Session $session, Router $router, ContentActionsMenu $contentActionsMenu, TableBuilder $tableBuilder, IbexaDropdown $ibexaDropdown)
    {
        parent::__construct($session, $router, $contentActionsMenu);
        $this->ibexaDropdown = $ibexaDropdown;
        $this->table = $tableBuilder->newTable()->build();
        $this->vatTable = $tableBuilder->newTable()
            ->withParentLocator(new VisibleCSSLocator('vatTable', '.ibexa-pc-assigned-vat-rates__list'))
            ->build();
    }

    public function addAttributeGroup(string $attributeGroupName): void
    {
        $availableAttributeGroupLabel = $this->getLocator('availableAttributeGroupLabelList');
        $listElement = $this->getHTMLPage()
            ->findAll($availableAttributeGroupLabel)
            ->getByCriterion(new ElementTextCriterion($attributeGroupName));
        $listElement->mouseOver();

        $availableAttributeGroupLabelsScript = sprintf("document.querySelector('[data-group-name=\'%s\']')", $attributeGroupName);

        $workspace = sprintf('document.querySelector(\'%s\')', $this->getLocator('attributeWorkspace')->getSelector());
        $this->getHTMLPage()->dragAndDrop($availableAttributeGroupLabelsScript, $workspace, $workspace);

        usleep(1500000);
    }

    public function addAttribute(string $attributeName): void
    {
        $availableAttributeLabel = $this->getLocator('availableAttributeLabelList');
        $listElement = $this->getHTMLPage()
            ->findAll($availableAttributeLabel)
            ->getByCriterion(new ElementTextCriterion($attributeName));
        $listElement->mouseOver();

        $availableAttributeLabelsScript = sprintf("document.querySelector('[data-attribute-name=\'%s\']')", $attributeName);

        $workspace = sprintf('document.querySelector(\'%s\')', $this->getLocator('attributeWorkspace')->getSelector());
        $this->getHTMLPage()->dragAndDrop($availableAttributeLabelsScript, $workspace, $workspace);

        usleep(1500000);
    }

    public function setFieldRequired(string $attributeType): void
    {
        $this->table->getTableRow(['Type' => $attributeType])->click($this->getLocator('toggleButtonRequired'));
    }

    public function setFieldUsedForProductVariants(string $attributeType): void
    {
        $this->table->getTableRow(['Type' => $attributeType])->click($this->getLocator('toggleButtonUsedForProductVariants'));
    }

    public function setVATRate(string $regionName, string $vatValue): void
    {
        $this->vatTable->getTableRow(['Region' => $regionName])->click($this->getLocator('vatDropdown'));
        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOption($vatValue);
    }

    public function specifyLocators(): array
    {
        return array_merge(parent::specifyLocators(), [
            new VisibleCSSLocator('availableAttributeGroupLabelList', '.ibexa-available-attribute-item-group .ibexa-available-attribute-item-group__header'),
            new VisibleCSSLocator('availableAttributeLabelList', '.ibexa-pc-attributes-sidebar__list > li > ul'),
            new VisibleCSSLocator('attributeWorkspace', '.ibexa-pc-attributes-empty-drop-zone'),
            new VisibleCSSLocator('toggleButtonRequired', '.ibexa-table__row td.ibexa-table__cell:nth-of-type(4) .ibexa-toggle--checkbox'),
            new VisibleCSSLocator('toggleButtonUsedForProductVariants', '.ibexa-table__row td.ibexa-table__cell:nth-of-type(5) .ibexa-toggle--checkbox'),
            new VisibleCSSLocator('vatDropdown', '.ibexa-dropdown'),
        ]);
    }
}
