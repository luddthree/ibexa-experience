<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Page;

use Behat\Mink\Session;
use Ibexa\AdminUi\Behat\Component\IbexaDropdown;
use Ibexa\Behat\API\Facade\ContentFacade;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;
use Ibexa\Behat\Browser\Routing\Router;
use Ibexa\VersionComparison\Behat\Component\VersionPreview;

abstract class BaseComparisonPage extends Page
{
    private ContentFacade $contentFacade;

    protected int $expectedContentId;

    protected int $expectedRightVersionNumber;

    protected int $expectedLeftVersionNumber;

    protected VersionPreview $versionPreview;

    protected IbexaDropdown $ibexaDropdown;

    public function __construct(
        Session $session,
        Router $router,
        ContentFacade $contentFacade,
        VersionPreview $versionPreview,
        IbexaDropdown $ibexaDropdown
    ) {
        parent::__construct($session, $router);
        $this->contentFacade = $contentFacade;
        $this->versionPreview = $versionPreview;
        $this->ibexaDropdown = $ibexaDropdown;
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('header'))->assert()->textEquals('Comparing versions');
    }

    public function selectRightSideVersion(string $versionNumber)
    {
        $this->getHTMLPage()->find($this->getLocator('rightSideDropdown'))->click();
        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOptionByValueFragment($versionNumber);
    }

    public function selectLeftSideVersion(string $versionNumber): void
    {
        $this->getHTMLPage()->find($this->getLocator('leftSideDropdown'))->click();
        $this->ibexaDropdown->verifyIsLoaded();
        $this->ibexaDropdown->selectOptionByValueFragment($versionNumber);
    }

    public function verifyLeftSideVersion(string $leftSideVersion)
    {
        $this->getHTMLPage()->find($this->getLocator('leftSideDropdownValue'))->assert()->textContains($leftSideVersion);
    }

    public function verifyRightSideVersion(string $rightSideVersionNumber): void
    {
        $this->getHTMLPage()->find($this->getLocator('rightSideDropdownValue'))->assert()->textContains($rightSideVersionNumber);
    }

    public function setExpectedVersionComparisonData(string $locationPath, int $leftVersionNumber, int $rightVersionNumber): void
    {
        $this->expectedContentId = $this->contentFacade->getContentByLocationURL($locationPath)->id;
        $this->expectedRightVersionNumber = $rightVersionNumber;
        $this->expectedLeftVersionNumber = $leftVersionNumber;
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('header', '.ibexa-version-compare-header__title'),
            new VisibleCSSLocator('leftSideDropdownValue', '#version_comparison_version_a .ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('leftSideDropdown', '#version_comparison_version_a .ibexa-dropdown'),
            new VisibleCSSLocator('rightSideDropdownValue', '#version_comparison_version_b .ibexa-dropdown__selection-info'),
            new VisibleCSSLocator('rightSideDropdown', '#version_comparison_version_b .ibexa-dropdown'),
        ];
    }
}
