<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\Values;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

final class EventActionInput extends ValueObject
{
    /** @var \Ibexa\Contracts\Calendar\EventAction\EventActionContext */
    public $context;
}

class_alias(EventActionInput::class, 'EzSystems\EzPlatformCalendarBundle\REST\Values\EventActionInput');
