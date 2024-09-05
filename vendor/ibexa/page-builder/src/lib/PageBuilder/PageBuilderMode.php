<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\PageBuilder;

class PageBuilderMode
{
    public const MODE_EDIT = 'edit';
    public const MODE_PREVIEW = 'preview';
    public const MODE_PREVIEW_NON_LOCATION = 'preview_non_location';
    public const MODE_CREATE = 'create';
    public const MODE_TRANSLATE = 'translate';
    public const MODE_TRANSLATE_WITHOUT_BASE_LANGUAGE = 'translate_without_base_language';
}

class_alias(PageBuilderMode::class, 'EzSystems\EzPlatformPageBuilder\PageBuilder\PageBuilderMode');
