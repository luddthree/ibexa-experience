<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Menu\Event;

final class ConfigureMenuEventName
{
    public const SITE_CREATE_SIDEBAR_RIGHT = 'ezplatform.sitefactory.menu_configure.site.create_actions';
    public const SITE_EDIT_SIDEBAR_RIGHT = 'ezplatform.sitefactory.menu_configure.site.edit_actions';
    public const SITE_VIEW_SIDEBAR_RIGHT = 'ezplatform.sitefactory.menu_configure.site.view_actions';
}

class_alias(ConfigureMenuEventName::class, 'EzSystems\EzPlatformSiteFactoryBundle\Menu\Event\ConfigureMenuEventName');
