<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

use Ibexa\Contracts\Calendar\Cursor;

class ScheduledEntryIdProvider
{
    public function fromCursor(?Cursor $cursor, string $typeIdentifier): ?int
    {
        $prefixDecorator = new PrefixDecorator($typeIdentifier);

        if ($cursor !== null && $cursor->getEventType() === $typeIdentifier) {
            return (int)$prefixDecorator->undecorate($cursor->getEventId());
        }

        return null;
    }
}

class_alias(ScheduledEntryIdProvider::class, 'EzSystems\EzPlatformPageFieldType\Calendar\ScheduledEntryIdProvider');
