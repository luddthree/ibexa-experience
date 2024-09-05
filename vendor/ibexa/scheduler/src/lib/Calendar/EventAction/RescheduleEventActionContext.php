<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar\EventAction;

use DateTimeImmutable;
use DateTimeInterface;
use Ibexa\Contracts\Calendar\EventAction\EventActionContext;
use Ibexa\Contracts\Calendar\EventCollection;

final class RescheduleEventActionContext extends EventActionContext
{
    /** @var \DateTimeImmutable */
    private $dateTime;

    public function __construct(EventCollection $events, DateTimeInterface $dateTime)
    {
        parent::__construct($events);

        if (!($dateTime instanceof DateTimeImmutable)) {
            $dateTime = DateTimeImmutable::createFromFormat(
                'U',
                (string)$dateTime->getTimestamp(),
                $dateTime->getTimezone()
            );
        }

        $this->dateTime = $dateTime;
    }

    public function getDateTime(): DateTimeImmutable
    {
        return $this->dateTime;
    }
}

class_alias(RescheduleEventActionContext::class, 'EzSystems\DateBasedPublisher\Core\Calendar\EventAction\RescheduleEventActionContext');
