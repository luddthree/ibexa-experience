<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class Timeline extends Component
{
    public function getEventDetails(int $position): string
    {
        return $this->getHTMLPage()
            ->findAll($this->getLocator('event'))
            ->toArray()[$position - 1]
            ->find($this->getLocator('eventName'))
            ->getText();
    }

    public function goTo($position): void
    {
        $this->getHTMLPage()
            ->findAll($this->getLocator('event'))
            ->toArray()[$position - 1]
            ->find($this->getLocator('eventName'))
            ->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('timelinePanel'))
            ->assert()->isVisible()
            ->find($this->getLocator('timelineHeader'))
            ->assert()->textEquals('Schedule');

        $this->getHTMLPage()->find($this->getLocator('eventList'))->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('timelineTitle', '.c-events-table__title'),
            new VisibleCSSLocator('eventsTable', '.c-events-table.c-events-table--is-visible'),
            new VisibleCSSLocator('isFuturePreview', '.ibexa-future-preview'),
            new VisibleCSSLocator('timelinePanel', '.c-pb-schedule-config-panel'),
            new VisibleCSSLocator('timelineHeader', '.ibexa-pb-config-panel__title'),
            new VisibleCSSLocator('eventList', '.c-pb-events-list'),
            new VisibleCSSLocator('event', '.c-pb-events-list__item'),
            new VisibleCSSLocator('eventName', '.ibexa-pb-event-item__event-name'),
        ];
    }
}
