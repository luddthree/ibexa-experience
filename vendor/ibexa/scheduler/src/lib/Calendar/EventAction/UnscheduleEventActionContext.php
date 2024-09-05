<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar\EventAction;

use Ibexa\Contracts\Calendar\EventAction\EventActionContext;

final class UnscheduleEventActionContext extends EventActionContext
{
}

class_alias(UnscheduleEventActionContext::class, 'EzSystems\DateBasedPublisher\Core\Calendar\EventAction\UnscheduleEventActionContext');
