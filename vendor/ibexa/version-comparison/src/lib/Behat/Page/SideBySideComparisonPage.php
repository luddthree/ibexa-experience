<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\VersionComparison\Behat\Page;

use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use PHPUnit\Framework\Assert;

final class SideBySideComparisonPage extends BaseComparisonPage
{
    public function verifyRightSideFieldData(string $fieldName, string $fieldTypeIdentifier, array $expectedValue)
    {
        $this->versionPreview->verifyRightSideFieldHasCorrectValues($fieldName, $expectedValue, $fieldTypeIdentifier);
    }

    public function verifyLeftSideFieldData($fieldName, $fieldTypeIdentifier, $expectedValue)
    {
        $this->versionPreview->verifyLeftSideFieldHasCorrectValues($fieldName, $expectedValue, $fieldTypeIdentifier);
    }

    public function switchToSingleColumnView()
    {
        $this->getHTMLPage()->find($this->getLocator('switchToSingleColumn'))->click();
    }

    protected function specifyLocators(): array
    {
        return array_merge(
            parent::specifyLocators(),
            [
                new VisibleCSSLocator('switchToSingleColumn', '.ibexa-version-compare-menu__type-selector--unified'),
                new VisibleCSSLocator('splitButton', '.ibexa-version-compare-menu__type-selector--split'),
                new VisibleCSSLocator('activeButton', 'ibexa-version-compare-menu__type-selector--active'),
            ]
        );
    }

    public function verifyIsLoaded(): void
    {
        parent::verifyIsLoaded();
        Assert::assertTrue($this->getHTMLPage()->find($this->getLocator('splitButton'))
            ->hasClass($this->getLocator('activeButton')->getSelector()));
    }

    public function getName(): string
    {
        return 'Side by side version comparison';
    }

    protected function getRoute(): string
    {
        return sprintf(
            'version/side-by-side-comparison/%d/%d/%d',
            $this->expectedContentId,
            $this->expectedRightVersionNumber,
            $this->expectedLeftVersionNumber
        );
    }
}
