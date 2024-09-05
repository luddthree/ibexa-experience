<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Component;

use Behat\Mink\Session;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class WorkflowEventHistoryPopup extends Component
{
    private $workflowEventHistory;

    public function __construct(Session $session, WorkflowEventHistory $workflowEventHistory)
    {
        parent::__construct($session);
        $this->workflowEventHistory = $workflowEventHistory;
    }

    /**
     * @return array[] Array of arrays with keys: event, message
     */
    public function getEntries(): array
    {
        return $this->workflowEventHistory->getEntries();
    }

    public function close()
    {
        $this->getHTMLPage()->find($this->getLocator('closeButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('title'))
            ->assert()->textEquals('Event timeline');
        $this->workflowEventHistory->verifyIsLoaded();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('closeButton', '.ibexa-popup--workflow.show .close'),
            new VisibleCSSLocator('title', '.ibexa-popup--workflow.show .modal-title'),
        ];
    }
}
