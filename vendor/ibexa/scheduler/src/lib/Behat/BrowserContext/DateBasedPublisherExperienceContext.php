<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Scheduler\Behat\BrowserContext;

use Ibexa\AdminUi\Behat\Component\ContentActionsMenu;
use Ibexa\AdminUi\Behat\Component\Notification;
use Ibexa\PageBuilder\Behat\Page\PageBuilderEditor;
use Ibexa\Scheduler\Behat\Component\DateBasedPublisherPopup;

class DateBasedPublisherExperienceContext extends DateBasedPublisherContext
{
    /** @var \Ibexa\PageBuilder\Behat\Page\PageBuilderEditor */
    private $pageBuilderEditor;

    public function __construct(
        DateBasedPublisherPopup $dateBasedPublisherPopup,
        ContentActionsMenu $contentActionsMenu,
        Notification $notification,
        PageBuilderEditor $pageBuilderEditor
    ) {
        parent::__construct($dateBasedPublisherPopup, $contentActionsMenu, $notification);
        $this->pageBuilderEditor = $pageBuilderEditor;
    }

    /**
     * @Given I publish later from Page Builder
     */
    public function publishLaterInPageBuilderEditor()
    {
        $this->pageBuilderEditor->clickMenuButton('Publish later', 'Publish');
        $this->dateBasedPublisherPopup->verifyIsLoaded();
        $this->dateBasedPublisherPopup->setDate(new \DateTime('+1Day'));
        $this->dateBasedPublisherPopup->setTime('16', '45');
        $this->dateBasedPublisherPopup->confirm();
    }
}
