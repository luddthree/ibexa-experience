<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Context;

use Behat\Gherkin\Node\TableNode;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Page\ContentUpdateItemPage;
use Ibexa\Behat\Browser\Page\Preview\PagePreviewRegistry;
use Ibexa\PageBuilder\Behat\Page\PageBuilderEditor;
use Ibexa\Workflow\Behat\Component\WorkflowEventHistory;
use Ibexa\Workflow\Behat\Component\WorkflowEventHistoryPopup;
use Ibexa\Workflow\Behat\Component\WorkflowPopup;
use Ibexa\Workflow\Behat\Page\DashboardPage;
use PHPUnit\Framework\Assert;

class WorkflowExperienceContext extends WorkflowContext
{
    /** @var \Ibexa\PageBuilder\Behat\Page\PageBuilderEditor */
    private $pageBuilderEditor;

    /** @var \Ibexa\Behat\Browser\Page\Preview\PagePreviewRegistry */
    private $pagePreviewRegistry;

    public function __construct(
        ContentActionsMenu $contentActionsMenu,
        WorkflowPopup $workflowPopup,
        DashboardPage $dashboardPage,
        WorkflowEventHistoryPopup $workflowEventHistoryPopup,
        WorkflowEventHistory $workflowEventHistory,
        ContentUpdateItemPage $contentUpdateItemPage,
        PageBuilderEditor $pageBuilderEditor,
        PagePreviewRegistry $pagePreviewRegistry
    ) {
        parent::__construct($contentActionsMenu, $workflowPopup, $dashboardPage, $workflowEventHistoryPopup, $workflowEventHistory, $contentUpdateItemPage);
        $this->pageBuilderEditor = $pageBuilderEditor;
        $this->pagePreviewRegistry = $pagePreviewRegistry;
    }

    /**
     * @Given workflow history contains events for viewed Page :pageName
     */
    public function workflowHistoryForPageContains(string $pageName, TableNode $workflowHistoryData)
    {
        $previewedPage = $this->pagePreviewRegistry->getPreview('landing_page');
        $previewedPage->setExpectedPreviewData(['title' => $pageName]);
        $this->pageBuilderEditor->setPreviewedPage($previewedPage);
        $this->pageBuilderEditor->verifyIsLoaded();
        $this->pageBuilderEditor->verifyPreviewData();
        $this->pageBuilderEditor->toggleFieldsForm();

        $expectedEntries = [];
        foreach ($workflowHistoryData->getHash() as $dataRow) {
            $expectedEntries[] = ['event' => $dataRow['Event'], 'message' => $dataRow['Message']];
        }

        Assert::assertEquals($expectedEntries, $this->workflowEventHistoryPopup->getEntries());

        $this->pageBuilderEditor->toggleFieldsForm();
    }
}
