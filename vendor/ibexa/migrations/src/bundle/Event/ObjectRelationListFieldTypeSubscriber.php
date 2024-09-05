<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Event;

use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ObjectRelationListFieldTypeSubscriber implements EventSubscriberInterface
{
    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                ['convertLocationIds', -100],
            ],
        ];
    }

    public function convertLocationIds(FieldValueFromHashEvent $event): void
    {
        if ($event->getFieldTypeIdentifier() !== 'ezobjectrelationlist') {
            return;
        }

        $hash = $event->getHash();
        if (!isset($hash['locationIds']) && !isset($hash['locationRemoteIds'])) {
            return;
        }

        $contentIds = array_flip($hash['destinationContentIds'] ?? []);

        $locationIds = $hash['locationIds'] ?? [];
        foreach ($locationIds as $locationId) {
            $location = $this->locationService->loadLocation($locationId);
            $contentId = $location->getContentInfo()->id;
            $contentIds[$contentId] = true;
        }

        $locationRemoteIds = $hash['locationRemoteIds'] ?? [];
        foreach ($locationRemoteIds as $locationRemoteId) {
            $location = $this->locationService->loadLocationByRemoteId($locationRemoteId);
            $contentId = $location->getContentInfo()->id;
            $contentIds[$contentId] = true;
        }

        $contentIds = array_keys($contentIds);

        $event->setHash(array_merge($hash, [
            'destinationContentIds' => $contentIds,
        ]));
    }
}
