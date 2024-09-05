<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage;
use Ibexa\Workflow\Behat\Component\WorkflowEventHistory;
use Ibexa\Workflow\Behat\Component\WorkflowEventHistoryPopup;
use Ibexa\Workflow\Behat\Component\WorkflowPopup;
use Ibexa\Workflow\Behat\Page\DashboardPage;
use PHPUnit\Framework\Assert;

class WorkflowContext implements Context
{
    /** @var \Ibexa\AdminUi\Behat\Component\ContentActionsMenu */
    private $contentActionsMenu;

    /** @var \Ibexa\Workflow\Behat\Component\WorkflowPopup */
    private $workflowPopup;

    /** @var \Ibexa\Workflow\Behat\Page\DashboardPage */
    private $dashboardPage;

    /** @var \Ibexa\Workflow\Behat\Component\WorkflowEventHistoryPopup */
    protected $workflowEventHistoryPopup;

    /** @var \Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage */
    private $contentUpdateItemPage;

    /** @var \Ibexa\Workflow\Behat\Component\WorkflowEventHistory */
    private $workflowEventHistory;

    public function __construct(
        ContentActionsMenu $contentActionsMenu,
        WorkflowPopup $workflowPopup,
        DashboardPage $dashboardPage,
        WorkflowEventHistoryPopup $workflowEventHistoryPopup,
        WorkflowEventHistory $workflowEventHistory,
        ContentUpdateItemPage $contentUpdateItemPage
    ) {
        $this->contentActionsMenu = $contentActionsMenu;
        $this->workflowPopup = $workflowPopup;
        $this->dashboardPage = $dashboardPage;
        $this->workflowEventHistoryPopup = $workflowEventHistoryPopup;
        $this->contentUpdateItemPage = $contentUpdateItemPage;
        $this->workflowEventHistory = $workflowEventHistory;
    }

    /**
     * @Given I transition to :transitionName with message :message
     * @Given I transition to :transitionName with message :message for reviewer :reviewer
     */
    public function transitionToStageWithMessage(string $transitionName, string $message, string $reviewer = null)
    {
        $this->contentActionsMenu->verifyIsLoaded();
        $this->contentActionsMenu->clickButton($transitionName);

        $this->workflowPopup->verifyIsLoaded();
        if ($reviewer !== null) {
            $this->workflowPopup->selectReviewer($reviewer);
        }
        $this->workflowPopup->setMessage($message);
        $this->workflowPopup->submit();
    }

    /**
     * @Given User :reviewer cannot be chosen as reviewer for :transitionName workflow transition
     */
    public function veifyIfReviewerIsNotSelectable(string $transitionName, string $reviewer)
    {
        $this->contentActionsMenu->verifyIsLoaded();
        $this->contentActionsMenu->clickButton($transitionName);

        $this->workflowPopup->verifyIsLoaded();
        $this->workflowPopup->verifyReviewerIsNotSelectable($reviewer);
    }

    /**
     * @Given the dashboard review queue contains :draftName draft in :stageName stage
     */
    public function dashboardReviewQueueContainsDraft(string $draftName, string $stageName)
    {
        Assert::assertEquals($stageName, $this->dashboardPage->getStatusForDraftInReviewQueue($draftName));
    }

    /**
     * @Given "Drafts to review" tab in :tableName table contains :draftName draft in :stageName stage
     */
    public function draftsUnderReviewTabContainsDraft(string $draftName, string $tableName, string $stageName)
    {
        $this->dashboardPage->switchTab($tableName, 'Drafts to review');
        Assert::assertEquals($stageName, $this->dashboardPage->getStatusForDraftInDraftsToReview($draftName));
    }

    /**
     * @Given I verify Workflow history table for :draftName:
     */
    public function verifyWorkflowHistoryInPopup(string $draftName, TableNode $workflowHistoryData)
    {
        $this->dashboardPage->verifyIsLoaded();
        $this->dashboardPage->openWorkflowChart($draftName);

        $expectedEntries = array_map(static function (array $row) {
            return [
                'event' => $row['Event'],
                'message' => $row['Message'],
            ];
        }, $workflowHistoryData->getHash());

        $this->workflowEventHistoryPopup->verifyIsLoaded();
        Assert::assertEquals($expectedEntries, $this->workflowEventHistoryPopup->getEntries());

        $this->workflowEventHistoryPopup->close();
    }

    /**
     * @Given I start reviewing :draftName item
     */
    public function startReviewingDraftInStage(string $draftName)
    {
        $this->dashboardPage->verifyIsLoaded();
        $this->dashboardPage->editDraftFromReviewQueue($draftName);
    }

    /**
     * @Then the Dashboard review queue does not contain an item named :itemName
     */
    public function theDashboardReviewQueueDoesNotContainAnItemNamed(string $itemName): void
    {
        Assert::assertFalse($this->dashboardPage->reviewQueueContainsDraft($itemName));
    }

    /**
     * @Given workflow history for :contentItem contains events:
     */
    public function verifyWorkflowHistoryContains(string $contentItem, TableNode $workflowHistoryData)
    {
        $this->contentUpdateItemPage->setExpectedPageTitle($contentItem);
        $this->contentUpdateItemPage->verifyIsLoaded();

        $expectedEntries = [];
        foreach ($workflowHistoryData->getHash() as $dataRow) {
            $expectedEntries[] = ['event' => $dataRow['Event'], 'message' => $dataRow['Message']];
        }

        Assert::assertEquals($expectedEntries, $this->workflowEventHistoryPopup->getEntries());
    }
}
