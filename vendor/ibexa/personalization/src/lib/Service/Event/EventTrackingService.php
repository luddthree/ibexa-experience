<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Event;

use Ibexa\Personalization\Client\Consumer\Event\EventTrackingDataSenderInterface;

final class EventTrackingService implements EventTrackingServiceInterface
{
    private EventTrackingDataSenderInterface $eventTrackingDataSender;

    public function __construct(EventTrackingDataSenderInterface $eventTrackingDataSender)
    {
        $this->eventTrackingDataSender = $eventTrackingDataSender;
    }

    public function handleEvent(string $userIdentifier, string $outputContentTypeId): void
    {
        $this->eventTrackingDataSender->sendEvent($userIdentifier, $outputContentTypeId);
    }
}
