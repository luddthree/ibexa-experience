<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Page;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Workflow\Behat\Component\Table\WorkflowListTable;

class WorkflowAdminPage extends Page
{
    /** @var \Ibexa\Workflow\Behat\Component\Table\WorkflowListTable */
    private $workflowListTable;

    public function __construct(Session $session, Router $router, WorkflowListTable $workflowListTable)
    {
        parent::__construct($session, $router);
        $this->workflowListTable = $workflowListTable;
    }

    public function verifyElements(): void
    {
        $this->getHTMLPage()->find($this->getLocator('firstTableHeader'))->assert()->textEquals('Workflow information');
    }

    public function verifyDiagramExists()
    {
        $this->getHTMLPage()->findAll($this->getLocator('diagramSelector'))->single()->assert()->isVisible();
    }

    public function isElementInTable(string $contentItemName, string $stageName)
    {
        return $this->workflowListTable->isElementPresentInTable($contentItemName, $stageName);
    }

    public function switchTab(string $tabName)
    {
        $this->getHTMLPage()->findAll($this->getLocator('tableTabSelector'))->getByCriterion(new ElementTextCriterion($tabName))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->verifyDiagramExists();
    }

    public function getName(): string
    {
        return 'Workflow Admin';
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('firstTableHeader', '.tab-pane.active div.ibexa-table-header:nth-of-type(1)'),
            new VisibleCSSLocator('tableTabSelector', '.ibexa-tabs .nav-item'),
            new VisibleCSSLocator('diagramSelector', '.ibexa-workflow-diagram'),
        ];
    }

    protected function getRoute(): string
    {
        return 'workflow/list';
    }
}
