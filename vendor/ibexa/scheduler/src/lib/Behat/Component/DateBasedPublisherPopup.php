<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\Behat\Component;

use Behat\Mink\Session;
use DateTimeInterface;
use Ibexa\AdminUi\Behat\Component\DateAndTimePopup;
use Ibexa\Behat\Browser\Component\Component;
use Ibexa\Behat\Browser\Element\Condition\ElementTransitionHasEndedCondition;
use Ibexa\Behat\Browser\Element\Criterion\ElementTextCriterion;
use Ibexa\Behat\Browser\Locator\VisibleCSSLocator;

class DateBasedPublisherPopup extends Component
{
    /** @var \Ibexa\AdminUi\Behat\Component\DateAndTimePopup */
    private $dateAndTimePopup;

    public function __construct(Session $session, DateAndTimePopup $dateAndTimePopup)
    {
        parent::__construct($session);
        $this->dateAndTimePopup = $dateAndTimePopup;
    }

    public function confirm(): void
    {
        $this->getHTMLPage()->findAll($this->getLocator('confirmButton'))->getByCriterion(new ElementTextCriterion('Confirm'))->click();
    }

    public function verifyIsLoaded(): void
    {
        $this->getHTMLPage()
            ->setTimeout(3)
            ->setTimeout(5)
            ->waitUntilCondition(
                new ElementTransitionHasEndedCondition(
                    $this->getHTMLPage(),
                    $this->getLocator('popupContainer')
                )
            )
            ->find($this->getLocator('header'))
            ->assert()->textEquals('Future publication settings');
    }

    public function setDate(DateTimeInterface $date)
    {
        $this->dateAndTimePopup->setDate($date);
    }

    public function setTime(int $hour, int $minute)
    {
        $this->dateAndTimePopup->setTime($hour, $minute);
    }

    protected function specifyLocators(): array
    {
        return [
            new VisibleCSSLocator('popupContainer', '.dbp-publish-later'),
            new VisibleCSSLocator('header', '.dbp-publish-later .ibexa-extra-actions__header'),
            new VisibleCSSLocator('confirmButton', '.ibexa-btn--confirm-schedule'),
        ];
    }
}
