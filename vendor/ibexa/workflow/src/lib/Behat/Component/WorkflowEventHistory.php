<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Workflow\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\ElementInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class WorkflowEventHistory extends Component
{
    /**
     * @return array[] Array of arrays with keys: event, message
     */
    public function getEntries(): array
    {
        return $this->getHTMLPage()
            ->setTimeout(5)
            ->findAll($this->getLocator('eventItems'))
            ->map(function (ElementInterface $element) {
                return [
                    'event' => $element->find($this->getLocator('event'))->getText(),
                    'message' => $element->find($this->getLocator('message'))->getText(),
                ];
            });
    }

    public function verifyIsLoaded(): void
    {
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('eventItems', '.ibexa-workflow-chart__items .ibexa-workflow-chart__item'),
            new VisibleCSSLocator('event', '.ibexa-workflow-event__name'),
            new VisibleCSSLocator('message', '.ibexa-workflow-event__desc'),
        ];
    }
}
