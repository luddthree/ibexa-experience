<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Event;

final class BlockPreviewEvents
{
    /** @var string */
    public const PAGE_CONTEXT = 'ezplatform.page_builder.block_preview.page_context';

    /** @var string */
    public const RESPONSE = 'ezplatform.page_builder.block_preview.response';

    /**
     * @param string $blockType
     *
     * @return string
     */
    public static function getBlockPreviewResponseEventName(string $blockType): string
    {
        return sprintf('%s.%s', static::RESPONSE, $blockType);
    }
}

class_alias(BlockPreviewEvents::class, 'EzSystems\EzPlatformPageBuilder\Event\BlockPreviewEvents');
