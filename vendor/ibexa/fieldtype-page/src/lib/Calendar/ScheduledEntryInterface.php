<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Calendar;

interface ScheduledEntryInterface
{
    public function getId(): string;
}

class_alias(ScheduledEntryInterface::class, 'EzSystems\EzPlatformPageFieldType\Calendar\ScheduledEntryInterface');
