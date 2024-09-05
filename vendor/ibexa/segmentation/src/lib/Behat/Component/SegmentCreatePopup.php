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

class SegmentCreatePopup extends Component
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('creatingANewSegmentWindow')))
            ->find($this->getLocator('creatingANewSegmentWindow'))->assert()->isVisible();
        $this->getHTMLPage()->setTimeout(3)
            ->find($this->getLocator('segmentPopupModalTitle'))
            ->assert()->textEquals('Create segment');
    }

    protected function specifyLocators(): array
    {
        return
            [
                new VisibleCSSLocator('creatingANewSegmentWindow', '#create-segment-modal'),
                new VisibleCSSLocator('identifierTextbox', '#segment_create_identifier'),
                new VisibleCSSLocator('nameTextbox', '#segment_create_name'),
                new VisibleCSSLocator('createButton', '#segment_create_create'),
                new VisibleCSSLocator('segmentPopupModalTitle', '#create-segment-modal .modal-title'),
        ];
    }

    public function setNameAndIdentifier(string $name, string $identifier): void
    {
        $this->getHTMLPage()->find($this->getLocator('identifierTextbox'))->setValue($identifier);
        $this->getHTMLPage()->find($this->getLocator('nameTextbox'))->setValue($name);
    }

    public function confirmNewSegmentAddition(): void
    {
        $this->getHTMLPage()->find($this->getLocator('createButton'))->click();
    }
}
