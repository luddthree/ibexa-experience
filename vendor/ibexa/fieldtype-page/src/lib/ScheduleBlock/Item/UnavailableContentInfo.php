<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\ScheduleBlock\Item;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;

class UnavailableContentInfo extends ContentInfo
{
    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct(array_replace($properties, ['status' => self::STATUS_TRASHED]));
    }
}

class_alias(UnavailableContentInfo::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Item\UnavailableContentInfo');
