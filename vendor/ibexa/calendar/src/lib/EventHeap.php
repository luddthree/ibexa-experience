<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Calendar;

use SplMaxHeap;

/**
 * Struct used internally to merge events from multiple data sources.
 *
 * @internal
 */
final class EventHeap extends SplMaxHeap
{
    /**
     * Compare elements in order to place them correctly in the heap while sifting up.
     *
     * @see https://php.net/manual/en/splmaxheap.compare.php.
     *
     * @param \Ibexa\Contracts\Calendar\Event $a
     * @param \Ibexa\Contracts\Calendar\Event $b
     *
     * @return int
     */
    protected function compare($a, $b): int
    {
        return $b->compareTo($a);
    }
}

class_alias(EventHeap::class, 'EzSystems\EzPlatformCalendar\Calendar\EventHeap');
