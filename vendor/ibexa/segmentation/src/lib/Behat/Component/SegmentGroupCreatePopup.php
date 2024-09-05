<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class SegmentGroupCreatePopup extends Component
{
    public function setNewSegmentGroupNameAndIdentifier(string $name, string $identifier): void
    {
        $this->getHTMLPage()->find($this->getLocator('segmentGroupNameInput'))->setValue($name);
        $this->getHTMLPage()->find($this->getLocator('segmentGroupIdentifierInput'))->setValue($identifier);
    }

    public function setNewSegmentNameAndIdentifier(string $name, string $identifier): void
    {
        $lastrow = $this->getHTMLPage()
            ->findAll(new VisibleCSSLocator('lastCell', '#create-segment-group-modal div.ibexa-scrollable-wrapper > table > tbody > tr'))->last();

        $nameInput = $lastrow->find(new VisibleCSSLocator('nameInput', ' [id*=name]'));
        $identifierInput = $lastrow->find(new VisibleCSSLocator('identifierInput', ' [id*=identifier]'));
        $nameInput->setValue($name);
        $identifierInput->setValue($identifier);
    }

    public function confirmSegmentGroupCreation(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createSegmentButton'))->click();
    }

    public function addNewSegmentRow(): void
    {
        $this->getHTMLPage()->find($this->getLocator('addSegmentButton'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('createSegmentPopup')))
            ->find($this->getLocator('createSegmentPopup'))->assert()->isVisible();
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find($this->getLocator('segmentGroupPopupModalTitle'))
            ->assert()->textEquals('Create segment group');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('createSegmentButton', '#segment_group_create_create'),
            new VisibleCSSLocator('createSegmentPopup', '#create-segment-group-modal'),
            new VisibleCSSLocator('addSegmentButton', '.ibexa-table-header__actions .ibexa-btn--add'),
            new VisibleCSSLocator('segmentGroupNameInput', '#segment_group_create_name'),
            new VisibleCSSLocator('segmentGroupIdentifierInput', '#segment_group_create_identifier'),
            new VisibleCSSLocator('segmentGroupPopupModalTitle', '#create-segment-group-modal .modal-title'),
        ];
    }
}
