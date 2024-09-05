<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Event\Subscriber;

use Ibexa\AdminUi\Menu\Event\ConfigureMenuEvent;

class ContentSidebarMenuSubscriber extends AbstractMenuSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            ConfigureMenuEvent::CONTENT_EDIT_SIDEBAR_RIGHT => [
                ['addTransitionButtonsToContentEdit', 0],
                ['removePublishButtonsOnContentEdit', -255],
            ],
            ConfigureMenuEvent::CONTENT_CREATE_SIDEBAR_RIGHT => [
                ['addTransitionButtonsToContentCreate', 0],
                ['removePublishButtonsOnContentCreate', -255],
            ],
        ];
    }
}

class_alias(ContentSidebarMenuSubscriber::class, 'EzSystems\EzPlatformWorkflow\Event\Subscriber\ContentSidebarMenuSubscriber');
