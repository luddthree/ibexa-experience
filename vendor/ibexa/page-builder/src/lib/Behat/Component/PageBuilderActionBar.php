<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Component;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\LanguagePicker;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\CSSLocator;
use Ibexa\Behat\Browser\Locator\CSSLocatorBuilder;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class PageBuilderActionBar extends Component
{
    /** @var \Ibexa\PageBuilder\Behat\Component\Timeline */
    private $timeline;

    /** @var \Ibexa\AdminUi\Behat\Component\LanguagePicker */
    private $languagePicker;

    public function __construct(Session $session, Timeline $timeline, LanguagePicker $languagePicker)
    {
        parent::__construct($session);
        $this->timeline = $timeline;
        $this->languagePicker = $languagePicker;
    }

    public function createNew(): void
    {
        $this->getHTMLPage()->find($this->getLocator('create'))->click();
    }

    public function toggleFieldsForm()
    {
        $this->getHTMLPage()->find($this->getLocator('showFieldsButton'))->click();
    }

    public function getCurrentMode(): string
    {
        $isChecked = $this->getHTMLPage()->find(
            CSSLocatorBuilder::base($this->getLocator('changeViewingMode'))
                ->withDescendant(new VisibleCSSLocator('input', 'input'))
                ->build()
        )->hasAttribute('checked');

        return $isChecked ? 'view' : 'edit';
    }

    public function getCurrentEditingMode(): string
    {
        $isChecked = $this->getHTMLPage()->find(
            $this->getLocator('showFieldsButton')
        )->hasClass('ibexa-btn--selected');

        return $isChecked ? 'field view' : 'page view';
    }

    public function switchToEditMode(string $language = null): void
    {
        $this->getHTMLPage()->find($this->getLocator('changeViewingMode'))->click();

        if ($this->languagePicker->isVisible()) {
            $this->languagePicker->chooseLanguage($language);
        }
    }

    public function toggleTimeline()
    {
        $this->getHTMLPage()->find($this->getLocator('timelineToggler'))->click();
    }

    public function toggleVisibility(): void
    {
        $this->getHTMLPage()->find($this->getLocator('visibilityToggler'))->click();
    }

    public function selectFromAdditionalMenu(string $label): void
    {
        $this->getHTMLPage()->find($this->getLocator('toolsToggler'))->click();
        $this->getHTMLPage()->findAll($this->getLocator('toolsMenuItem'))->getByCriterion(new ElementTextCriterion($label))->click();
    }

    public function verifyPreviewingInTheFuture(): void
    {
        $this->getHTMLPage()
            ->find($this->getLocator('futurePreviewPopupTitle'))
            ->assert()->textEquals('Previewing in the future');
    }

    public function canCurrentUserEdit(): bool
    {
        return false === $this->getHTMLPage()->find($this->getLocator('changeViewingMode'))->hasClass('disabled');
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(15)
            ->find($this->getLocator('isVisible'))
            ->assert()->isVisible();
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('isVisible', '.ibexa-pb-action-bar'),
            new VisibleCSSLocator('create', '.ibexa-page-info-bar__create-content'),
            new VisibleCSSLocator('showFieldsButton', '.ibexa-pb-action-bar__action-btn--show-fields'),
            new CSSLocator('changeViewingMode', '.ibexa-pb-action-bar__action-btn--layout-selector'),
            new VisibleCSSLocator('timelineToggler', '.ibexa-pb-action-bar__action-btn--scheduler'),
            new VisibleCSSLocator('visibilityToggler', '.ibexa-pb-action-bar__action-btn--visibility'),
            new VisibleCSSLocator('toolsToggler', '.ibexa-page-info-bar__tools-wrapper'),
            new VisibleCSSLocator('toolsMenuItem', '.ibexa-page-info-bar__tools-item'),
            new VisibleCSSLocator('elementsBoxToggler', '.c-pb-sidebar__toggler'),
            new VisibleCSSLocator('futurePreviewPopupTitle', '.c-pb-back-to-current-time__title'),
        ];
    }
}
