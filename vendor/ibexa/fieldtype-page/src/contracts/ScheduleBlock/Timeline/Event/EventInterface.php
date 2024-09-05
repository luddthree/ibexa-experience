<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Contracts\FieldTypePage\ScheduleBlock\Timeline\Event;

use DateTimeInterface;

interface EventInterface
{
    /**
     *  @return string
     */
    public function getId(): string;

    /**
     *  @return string
     */
    public function getType(): string;

    /**
     *  @return \DateTimeInterface
     */
    public function getDateTime(): DateTimeInterface;
}

class_alias(EventInterface::class, 'EzSystems\EzPlatformPageFieldType\ScheduleBlock\Timeline\Event\EventInterface');
