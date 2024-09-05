<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\FieldTypeAddress\Event;

final class Events
{
    private const MAP_FIELD_PATTERN = 'ibexa.address.field.%s';

    public static function getMapFieldEventName(
        string $identifier,
        ?string $type = null,
        ?string $country = null
    ): string {
        return sprintf(
            self::MAP_FIELD_PATTERN,
            implode('.', array_filter([
                $identifier,
                $type,
                $country,
            ]))
        );
    }

    /**
     * @return array<string>
     */
    public static function getMapFieldEventNames(string $identifier, string $type, ?string $country): array
    {
        $baseEventName = sprintf(self::MAP_FIELD_PATTERN, $identifier);

        $eventNames = [
            $baseEventName,
            $baseEventName . '.' . $type,
        ];

        if (null !== $country) {
            $eventNames[] = $baseEventName . '.' . $type . '.' . $country;
        }

        return $eventNames;
    }
}
