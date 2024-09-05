<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Connector\Dam\Event;

use Ibexa\Contracts\Connector\Dam\AssetIdentifier;
use Ibexa\Contracts\Connector\Dam\AssetSource;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class PublishAssetEventDispatcher implements EventSubscriberInterface
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface */
    private $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => 'emitPublishAssetEvent',
        ];
    }

    public function emitPublishAssetEvent(PublishVersionEvent $event)
    {
        $content = $event->getContent();

        foreach ($content->getFields() as $field) {
            if ($field->fieldTypeIdentifier !== 'ezimageasset') {
                continue;
            }
            /** @var \Ibexa\Connector\Dam\FieldType\Dam\Value $value */
            $value = $field->value;
            if ($value->source === null) {
                continue;
            }
            $this->eventDispatcher->dispatch(
                new PublishAssetEvent(
                    $content,
                    new AssetIdentifier($value->destinationContentId),
                    new AssetSource($value->source)
                )
            );
        }
    }
}

class_alias(PublishAssetEventDispatcher::class, 'Ibexa\Platform\Connector\Dam\Event\PublishAssetEventDispatcher');
