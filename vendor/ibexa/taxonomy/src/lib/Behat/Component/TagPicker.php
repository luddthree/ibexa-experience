<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Behat\Component;

use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementNotExistsCondition;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Locator\XPathLocator;

final class TagPicker extends Component
{
    private const EMPTY_SEARCH_TEXT = 'THISWILLRESULTINEMPTYRSEARCH';

    private string $expectedHeader;

    private string $expectedConfirmMessage;

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->waitUntilCondition(
                new ElementTransitionHasEndedCondition($this->getHTMLPage(), $this->getLocator('selectModal'))
            );

        $this->getHTMLPage()
            ->find($this->getLocator('modalHeader'))
            ->assert()->textEquals($this->expectedHeader);
    }

    private function searchForTag(string $tagName): void
    {
        $this->getHTMLPage()->find($this->getLocator('search'))->setValue(self::EMPTY_SEARCH_TEXT);
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementNotExistsCondition($this->getHTMLPage(), $this->getLocator('tag')));
        $this->getHTMLPage()->find($this->getLocator('search'))->setValue($tagName);
        $this->getHTMLPage()
            ->setTimeout(5)
            ->waitUntilCondition(new ElementExistsCondition($this->getHTMLPage(), $this->getLocator('tag')));
    }

    public function selectTag(string $tagPath): void
    {
        $tagPathParts = explode('/', $tagPath);
        $tagToSelect = end($tagPathParts);

        $this->searchForTag($tagToSelect);

        $this->getHTMLPage()
            ->setTimeout(5)
            ->find($this->getLocator('selectModal'))
            ->find(new XPathLocator('tag', sprintf('//*[normalize-space(text())="%s"]', $tagToSelect)))
            ->click();
    }

    public function confirm(): void
    {
        $this->getHTMLPage()
            ->find(
                new XPathLocator(
                    'confirmButton',
                    sprintf('//button[normalize-space(text())="%s"]', $this->expectedConfirmMessage)
                )
            )
            ->click();
    }

    public function setExpectedHeader(string $header): void
    {
        $this->expectedHeader = $header;
    }

    public function setExpectedConfirmMessage(string $confirmMessage): void
    {
        $this->expectedConfirmMessage = $confirmMessage;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('confirmButton', '.ibexa-btn--confirm'),
            new VisibleCSSLocator('selectModal', '.ibexa-modal--taxonomy-tree-modal'),
            new VisibleCSSLocator('search', '.ibexa-taxonomy-tree .c-tb-search .ibexa-input'),
            new VisibleCSSLocator('modalHeader', '.ibexa-modal--taxonomy-tree-modal .modal-header'),
            new VisibleCSSLocator('tag', '.ibexa-modal--taxonomy-tree-modal.show .c-tb-list-item-single__element'),
            new VisibleCSSLocator(
                'notExpandedTag',
                '.ibexa-modal--taxonomy-tree-modal .c-tb-list-item-single--has-sub-items:not(.c-tb-list-item-single--expanded)'
            ),
        ];
    }
}
