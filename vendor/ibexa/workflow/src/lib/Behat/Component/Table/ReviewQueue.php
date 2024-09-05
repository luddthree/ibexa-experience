<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Component\Table;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Component\Table\TableInterface;
use Ibexa\AdminUi\Behat\Component\Table\TableRow;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class ReviewQueue extends Component implements TableInterface
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\TableInterface */
    private $table;

    public function __construct(Session $session, TableBuilder $tableBuilder)
    {
        parent::__construct($session);
        $this->table = $tableBuilder->newTable()->withParentLocator($this->getLocator('reviewQueue'))->build();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('workflowChartButton', '.ibexa-btn--workflow-chart'),
            new VisibleCSSLocator('reviewQueue', '.ibexa-card .ibexa-workflow-dashboard-table'),
            new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container'),
        ];
    }

    public function openWorkflowChart(string $contentItemName)
    {
        $row = $this->getTableRow(['Name' => $contentItemName]);
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());
        $row->click($this->getLocator('workflowChartButton'));
    }

    public function isEmpty(): bool
    {
        return $this->table->isEmpty();
    }

    public function hasElement(array $elementData): bool
    {
        return $this->table->hasElement($elementData);
    }

    public function hasElementOnCurrentPage(array $elementData): bool
    {
        return $this->table->hasElementOnCurrentPage($elementData);
    }

    public function getTableRow(array $elementData): TableRow
    {
        return $this->table->getTableRow($elementData);
    }

    public function getTableRowByIndex(int $rowIndex): TableRow
    {
        return $this->table->getTableRowByIndex($rowIndex);
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('reviewQueue'))->assert()->isVisible();
    }
}
