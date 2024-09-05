<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock\Item;

use Ibexa\Core\Repository\Values\Content\Location;

class UnavailableLocation extends Location
{
    public const STATUS_REMOVED = 2;

    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct(array_replace($properties, ['status' => self::STATUS_REMOVED]));
    }
}

class_alias(UnavailableLocation::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Item\UnavailableLocation');
