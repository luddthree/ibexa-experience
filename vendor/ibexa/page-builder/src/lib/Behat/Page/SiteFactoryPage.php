<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\PageBuilder\Behat\Page;

use Ibexa\Behat\Browser\Element\Criterion\ChildElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Behat\Browser\Page\Page;

class SiteFactoryPage extends Page
{
    public function goToLocationPreview(string $siteName): void
    {
        $rowWithSite = $this->getHTMLPage()->findAll($this->getLocator('tableRow'))
            ->getByCriterion(new ChildElementTextCriterion($this->getLocator('siteName'), $siteName));

        $hasPreviewButton = $rowWithSite->findAll($this->getLocator('locationPreviewButton'))->any();
        if ($hasPreviewButton) {
            $rowWithSite->find($this->getLocator('locationPreviewButton'))->click();
        } else {
            $rowWithSite->find($this->getLocator('languagePreviewLink'))->click();
        }
    }

    public function getName(): string
    {
        return 'Site Factory';
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('title'))->assert()->textEquals('Sites');
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('siteName', '.ibexa-site-factory-info__content :first-child'),
            new VisibleCSSLocator('tableRow', '.ibexa-table__row'),
            new VisibleCSSLocator('languagePreviewLink', '[data-original-title="Jump to Page Builder"]'),
            new VisibleCSSLocator('locationPreviewButton', '.btn[data-original-title="Location preview"]'),
            new VisibleCSSLocator('title', '.ibexa-page-title__title'),
        ];
    }

    protected function getRoute(): string
    {
        return 'site/list/56';
    }
}
