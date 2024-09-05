<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event;

final class PageEvents
{
    public const PRE_RENDER = 'ezplatform.ezlandingpage.page.render.pre';

    public const PERSISTENCE_FROM = 'ezplatform.ezlandingpage.page.persistence.from';
    public const PERSISTENCE_TO = 'ezplatform.ezlandingpage.page.persistence.to';

    public const ATTRIBUTE_SERIALIZATION = 'ezplatform.ezlandingpage.page.zone.block.attribute.serialization';
    public const ATTRIBUTE_DESERIALIZATION = 'ezplatform.ezlandingpage.page.zone.block.attribute.deserialization';

    /**
     * @param string $blockType
     *
     * @return string
     */
    public static function getAttributeSerializationEventName(string $blockType): string
    {
        return sprintf(self::ATTRIBUTE_SERIALIZATION . '.%s', $blockType);
    }

    /**
     * @param string $blockType
     *
     * @return string
     */
    public static function getAttributeDeserializationEventName(string $blockType): string
    {
        return sprintf(self::ATTRIBUTE_DESERIALIZATION . '.%s', $blockType);
    }
}

class_alias(PageEvents::class, 'EzSystems\EzPlatformPageFieldType\Event\PageEvents');
