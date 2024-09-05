<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\Behat\Page;

use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\LocatorInterface;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;
use Ibexa\Workflow\Behat\Page\DashboardPage as BaseDashboardPage;

class DashboardPage extends BaseDashboardPage
{
    protected function specifyLocators(): array
    {
        $createButtonIdentifier = 'createButton';

        $locators = array_filter(
            parent::specifyLocators(),
            static function (LocatorInterface $locator) use ($createButtonIdentifier) {
                return $locator->getIdentifier() !== $createButtonIdentifier;
            }
        );

        return array_merge(
            $locators,
            [
                new VisibleCSSLocator(
                    $createButtonIdentifier,
                    '[data-udw-title="Create content"]'
                ),
            ],
        );
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()->find($this->getLocator('pageTitle'))->assert()->textContains('Good');
        $this->getHTMLPage()->findAll($this->getLocator('tableTitle'))
            ->getByCriterion(new ElementTextCriterion('My content'))
            ->assert()->isVisible();
    }

    public function editDraftFromReviewQueue(string $draftName): void
    {
        $container = new VisibleCSSLocator('scrollableContainer', '.ibexa-back-to-top-scroll-container');
        $this->getHTMLPage()->find($container)->scrollToBottom($this->getSession());
        $this->getHTMLPage()
            ->setTimeout(3)
            ->find(new VisibleCSSLocator('backToTopWithTitle', '.ibexa-back-to-top__title--visible'))
            ->assert()->textEquals('Go to top');
        parent::editDraftFromReviewQueue($draftName);
    }
}
