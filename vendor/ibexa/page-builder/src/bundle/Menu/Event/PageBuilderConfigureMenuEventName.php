<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\Menu\Event;

class PageBuilderConfigureMenuEventName
{
    /** Infobar Actions */
    public const PAGE_BUILDER_INFOBAR_EDIT_MODE_ACTIONS = 'ezplatform_page_builder.menu_configure.infobar.edit.actions';
    public const PAGE_BUILDER_INFOBAR_CREATE_MODE_ACTIONS = 'ezplatform_page_builder.menu_configure.infobar.create.actions';

    /** Infobar Tools */
    public const PAGE_BUILDER_INFOBAR_PREVIEW_MODE_TOOLS = 'ezplatform_page_builder.menu_configure.infobar.preview.tools';
    public const PAGE_BUILDER_INFOBAR_EDIT_MODE_TOOLS = 'ezplatform_page_builder.menu_configure.infobar.edit.tools';
    public const PAGE_BUILDER_INFOBAR_CREATE_MODE_TOOLS = 'ezplatform_page_builder.menu_configure.infobar.create.tools';
    public const PAGE_BUILDER_INFOBAR_TRANSLATE_MODE_TOOLS = 'ezplatform_page_builder.menu_configure.infobar.create.tools';
}

class_alias(PageBuilderConfigureMenuEventName::class, 'EzSystems\EzPlatformPageBuilderBundle\Menu\Event\PageBuilderConfigureMenuEventName');
