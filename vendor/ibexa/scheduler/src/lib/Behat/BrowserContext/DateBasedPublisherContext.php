<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\Behat\BrowserContext;

use Behat\Behat\Context\Context;
use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\Notification;
use Ibexa\Scheduler\Behat\Component\DateBasedPublisherPopup;
use PHPUnit\Framework\Assert;

class DateBasedPublisherContext implements Context
{
    /** @var \Ibexa\Scheduler\Behat\Component\DateBasedPublisherPopup */
    protected $dateBasedPublisherPopup;

    /** @var \Ibexa\AdminUi\Behat\Component\ContentActionsMenu */
    private $contentActionsMenu;

    /** @var \Ibexa\AdminUi\Behat\Component\Notification */
    private $notification;

    public function __construct(
        DateBasedPublisherPopup $dateBasedPublisherPopup,
        ContentActionsMenu $contentActionsMenu,
        Notification $notification
    ) {
        $this->dateBasedPublisherPopup = $dateBasedPublisherPopup;
        $this->contentActionsMenu = $contentActionsMenu;
        $this->notification = $notification;
    }

    /**
     * @Given I publish later
     */
    public function publishLater()
    {
        $this->contentActionsMenu->clickButton('Publish later', 'Publish');
        $this->contentActionsMenu->verifyIsLoaded();

        $this->dateBasedPublisherPopup->verifyIsLoaded();
        $this->dateBasedPublisherPopup->setDate(new \DateTime('+1Day'));
        $this->dateBasedPublisherPopup->setTime('16', '45');
        $this->dateBasedPublisherPopup->confirm();
    }

    /**
     * @Then I am notified that content is scheduled for publishing
     */
    public function verifyNotificationAboutFuturePublishing()
    {
        $this->notification->verifyIsLoaded();
        $this->notification->verifyAlertSuccess();
        Assert::assertEquals('Scheduled content for publication.', $this->notification->getMessage());
    }
}
