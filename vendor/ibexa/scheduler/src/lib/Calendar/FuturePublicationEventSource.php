<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Scheduler\Calendar;

use Ibexa\Contracts\Calendar\EventType\EventTypeInterface;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Ibexa\Contracts\Scheduler\Repository\DateBasedPublishServiceInterface;
use Ibexa\Contracts\Scheduler\ValueObject\ScheduledEntry;

final class FuturePublicationEventSource extends AbstractDateBasedActionEventSource
{
    /** @var \Ibexa\Contracts\Core\Repository\LanguageService */
    private $languageService;

    public function __construct(
        DateBasedPublishServiceInterface $dateBasedService,
        EventTypeInterface $eventType,
        LanguageService $languageService
    ) {
        parent::__construct($dateBasedService, $eventType);

        $this->languageService = $languageService;
    }

    protected function buildEventDomainObject(
        EventTypeInterface $eventType,
        string $id,
        ScheduledEntry $scheduledscheduledEntry
    ): ScheduledEntryBasedEvent {
        return new FuturePublicationEvent(
            $eventType,
            $id,
            $scheduledscheduledEntry,
            $this->languageService->loadLanguage(
                $scheduledscheduledEntry->versionInfo->initialLanguageCode
            )
        );
    }
}

class_alias(FuturePublicationEventSource::class, 'EzSystems\DateBasedPublisher\Core\Calendar\FuturePublicationEventSource');
