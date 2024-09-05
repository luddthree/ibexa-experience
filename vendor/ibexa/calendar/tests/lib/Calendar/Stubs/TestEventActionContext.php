<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Calendar\Calendar\Stubs;

use Ibexa\Contracts\Calendar\EventAction\EventActionContext;

final class TestEventActionContext extends EventActionContext
{
}

class_alias(TestEventActionContext::class, 'EzSystems\EzPlatformCalendar\Tests\Calendar\Stubs\TestEventActionContext');
