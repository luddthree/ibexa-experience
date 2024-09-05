<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Calendar\EventType;

use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;

interface EventTypeRegistryInterface
{
    public function register(EventTypeInterface $type): void;

    public function hasType(string $identifier): bool;

    public function getType(string $identifier): EventTypeInterface;

    public function getTypes(): iterable;
}

class_alias(EventTypeRegistryInterface::class, 'EzSystems\EzPlatformCalendar\Calendar\EventType\EventTypeRegistryInterface');
