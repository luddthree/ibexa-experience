<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\Event;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Core\FieldType\Relation\Value;
use Ibexa\Migration\Event\FieldValueFromHashEvent;
use Ibexa\Migration\Event\HashFromFieldValueEvent;
use Ibexa\Migration\Event\MigrationEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ObjectRelationFieldTypeSubscriber implements EventSubscriberInterface
{
    private ContentService $contentService;

    public function __construct(ContentService $contentService)
    {
        $this->contentService = $contentService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            MigrationEvents::AFTER_HASH_FROM_FIELD_VALUE => [
                ['addRemoteContentId', -100],
            ],
            MigrationEvents::BEFORE_FIELD_VALUE_FROM_HASH => [
                ['convertRemoteContentId', -100],
            ],
        ];
    }

    public function addRemoteContentId(HashFromFieldValueEvent $event): void
    {
        $value = $event->getSpiFieldTypeValue();

        if (!$value instanceof Value) {
            return;
        }

        if ($value->destinationContentId === null) {
            return;
        }

        $hash = $event->getHash();

        $content = $this->contentService->loadContent($value->destinationContentId);
        $hash['contentRemoteId'] = $content->contentInfo->remoteId;

        $event->setHash($hash);
    }

    public function convertRemoteContentId(FieldValueFromHashEvent $event): void
    {
        if ($event->getFieldTypeIdentifier() !== 'ezobjectrelation') {
            return;
        }

        $hash = $event->getHash();

        if (!isset($hash['contentRemoteId'])) {
            return;
        }

        $content = $this->contentService->loadContentByRemoteId($hash['contentRemoteId']);

        $event->setHash(array_merge($hash, [
            'destinationContentId' => $content->id,
        ]));
    }
}
