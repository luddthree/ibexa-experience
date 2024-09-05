<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Context;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\Workflow\Behat\Page\WorkflowAdminPage;
use PHPUnit\Framework\Assert;

class WorkflowAdminContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Component\Table\Table */
    private $table;

    /** @var \Ibexa\Workflow\Behat\Page\WorkflowAdminPage */
    private $workflowAdminPage;

    public function __construct(TableBuilder $tableBuilder, WorkflowAdminPage $workflowAdminPage)
    {
        $this->table = $tableBuilder->newTable()->build();
        $this->workflowAdminPage = $workflowAdminPage;
    }

    /**
     * @Given there is a :name workflow with Stages :stages
     */
    public function thereIsAWorkflow(string $name, string $stageNames)
    {
        $parsedStageNames = str_replace(',', ' ', $stageNames);

        Assert::assertTrue($this->table->hasElement(['Name' => $name, 'Stages' => $parsedStageNames]));
    }

    /**
     * @Given I go to :workflowName details
     */
    public function goToWorkflowDetails(string $workflowName)
    {
        $this->table->getTableRow(['Name' => $workflowName])->goToItem();
    }

    /**
     * @Given there is a workflow diagram
     */
    public function thereIsAWorkflowDiagram()
    {
        $this->workflowAdminPage->verifyIsLoaded();
    }

    /**
     * @Given the item :itemName is in :stageName stage
     */
    public function thereIsItemInStage(string $itemName, string $stageName)
    {
        $this->workflowAdminPage->switchTab('Content under review');
        Assert::assertTrue($this->workflowAdminPage->isElementInTable($itemName, $stageName));
    }
}
