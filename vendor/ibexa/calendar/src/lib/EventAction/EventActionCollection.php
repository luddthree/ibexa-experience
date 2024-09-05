<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Calendar\EventAction;

use Ibexa\Contracts\Calendar\EventAction\EventActionCollection as BaseEventActionCollection;

/**
 * @deprecated 4.6.7 The "\Ibexa\Calendar\EventAction\EventActionCollection" class is deprecated, will be removed in 5.0.0. Use "\Ibexa\Contracts\Calendar\EventAction\EventActionCollection" instead.
 */
final class EventActionCollection extends BaseEventActionCollection
{
}

class_alias(\Ibexa\Calendar\EventAction\EventActionCollection::class, 'EzSystems\EzPlatformCalendar\Calendar\EventAction\EventActionCollection');
