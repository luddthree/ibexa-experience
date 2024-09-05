<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\Table\TableBuilder;
use Ibexa\AdminUi\Behat\Page\DashboardPage as OriginalDashboardPage;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\Workflow\Behat\Component\Table\ReviewQueue;

class DashboardPage extends OriginalDashboardPage
{
    /** @var \Ibexa\Workflow\Behat\Component\Table\ReviewQueue */
    private $reviewQueue;

    public function __construct(Session $session, Router $router, TableBuilder $tableBuilder, ReviewQueue $reviewQueue)
    {
        parent::__construct($session, $router, $tableBuilder);
        $this->reviewQueue = $reviewQueue;
    }

    public function reviewQueueContainsDraft(string $draftName): bool
    {
        return $this->reviewQueue->hasElement(['Name' => $draftName]);
    }

    public function openWorkflowChart(string $draftName)
    {
        $this->reviewQueue->openWorkflowChart($draftName);
    }

    public function editDraftFromReviewQueue(string $draftName): void
    {
        $row = $this->reviewQueue->getTableRow(['Name' => $draftName]);
        $this->getHTMLPage()->find($this->getLocator('scrollableContainer'))->scrollToBottom($this->getSession());
        $row->edit();
    }

    public function getStatusForDraftInDraftsToReview(string $draftName): string
    {
        $this->switchTab('My content', 'Drafts to review');

        return $this->getHTMLPage()
            ->find($this->getLocator('draftToReviewTable'))
            ->findAll($this->getLocator('tableRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('rowName'), $draftName))
            ->find($this->getLocator('rowStatus'))
            ->getText();
    }

    public function getStatusForDraftInReviewQueue(string $draftName): string
    {
        return $this->reviewQueue->getTableRow(['Name' => $draftName])->getCellValue('Status');
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('draftToReviewTable', 'div#ibexa-tab-dashboard-my-my-drafts-under-review'),
                new VisibleCSSLocator('tableRow', 'tr'),
                new VisibleCSSLocator('rowName', '.ibexa-table__cell--close-left'),
                new VisibleCSSLocator('rowStatus', '.badge'),
                new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container'),
            ]
        );
    }
}
