<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImagePicker\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementHasTextCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

final class ImagePicker extends Component
{
    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('title'))->assert()->textEquals('Image library');
    }

    public function selectImage(string $imageName): void
    {
        $this->getHTMLPage()
            ->setTimeout(10)
            ->findAll($this->getLocator('tile'))
            ->getByCriterion(
                new ChildElementTextCriterion($this->getLocator('tileTitle'), $imageName)
            )->click();
        $this->getHTMLPage()->find($this->getLocator('selectedItem'))->assert()->textEquals($imageName);
    }

    public function search(string $text): void
    {
        $this->getHTMLPage()->find($this->getLocator('search'))->setValue('THISWILLRESULTINEMPTYSEARCH');
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(
                new ElementHasTextCondition($this->getHTMLPage(), $this->getLocator('emptyResults'), 'No images yet')
            );
        $this->getHTMLPage()->find($this->getLocator('search'))->setValue($text);
    }

    public function confirm(): void
    {
        $this->getHTMLPage()->setTimeout(3)->find($this->getLocator('insertButton'))->click();
        $this->getHTMLPage()
            ->waitUntilCondition(
                new ElementNotExistsCondition(
                    $this->getHTMLPage(),
                    $this->getLocator('imagePickerContainer')
                )
            );
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('title', '.c-ip-top-bar__title'),
            new VisibleCSSLocator('search', '.ibexa-input-text-wrapper--search input'),
            new VisibleCSSLocator('tileTitle', '.ibexa-grid-view-item__title'),
            new VisibleCSSLocator('tile', '.ibexa-grid-view-item'),
            new VisibleCSSLocator('insertButton', '.c-ip-snackbar__insert-btn'),
            new VisibleCSSLocator('imagePickerContainer', '.c-ip-main-container'),
            new VisibleCSSLocator('emptyResults', '.ibexa-table__empty-table-info-text'),
            new VisibleCSSLocator('selectedItem', '.c-ip-snackbar__selection-info-item-content'),
        ];
    }
}
