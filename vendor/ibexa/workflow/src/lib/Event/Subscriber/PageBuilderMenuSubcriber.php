<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\AdminUi\Menu\ContentCreateRightSidebarBuilder;
use Ibexa\AdminUi\Menu\ContentEditRightSidebarBuilder;
use Ibexa\Bundle\PageBuilder\Menu\Builder\InfobarCreateModeActionsBuilder;
use Ibexa\Bundle\PageBuilder\Menu\Builder\InfobarEditModeActionsBuilder;
use Ibexa\Bundle\PageBuilder\Menu\Event\PageBuilderConfigureMenuEventName;
use Ibexa\Scheduler\Menu\ConfigureMenuListener as DateBasedPublisherMenu;

class PageBuilderMenuSubcriber extends AbstractMenuSubscriber
{
    protected const PUBLISH_MENU_ITEMS = [
        ContentCreateRightSidebarBuilder::ITEM__PUBLISH,
        ContentEditRightSidebarBuilder::ITEM__PUBLISH,
        InfobarCreateModeActionsBuilder::ITEM__PUBLISH,
        InfobarEditModeActionsBuilder::ITEM__PUBLISH,
        DateBasedPublisherMenu::MENU_DATE_BASED_PUBLISHER,
        DateBasedPublisherMenu::MENU_DATE_BASED_PUBLISHER_DISCARD,
    ];

    public static function getSubscribedEvents(): array
    {
        return [
            PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_EDIT_MODE_ACTIONS => [
                ['addTransitionButtonsToContentEdit', 0],
                ['removePublishButtonsOnContentEdit', -255],
            ],
            PageBuilderConfigureMenuEventName::PAGE_BUILDER_INFOBAR_CREATE_MODE_ACTIONS => [
                ['addTransitionButtonsToContentCreate', 0],
                ['removePublishButtonsOnContentCreate', -255],
            ],
        ];
    }
}

class_alias(PageBuilderMenuSubcriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\PageBuilderMenuSubcriber');
