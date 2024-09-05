<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Event\Subscriber\CachePurge;

use Ibexa\Contracts\Core\Persistence\Content\Location\Handler as LocationHandler;
use Ibexa\Contracts\Core\Persistence\URL\Handler as UrlHandler;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\HttpCache\Handler\ContentTagInterface;
use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;
use Ibexa\FieldTypePage\FieldType\LandingPage\Type;
use Ibexa\HttpCache\EventSubscriber\CachePurge\AbstractSubscriber;

class ContentEventsSubscriber extends AbstractSubscriber
{
    /** @var bool */
    private $isTranslationAware;

    public function __construct(
        PurgeClientInterface $purgeClient,
        LocationHandler $locationHandler,
        UrlHandler $urlHandler,
        bool $isTranslationAware
    ) {
        parent::__construct($purgeClient, $locationHandler, $urlHandler);

        $this->isTranslationAware = $isTranslationAware;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PublishVersionEvent::class => 'onPublishVersionEvent',
        ];
    }

    public function onPublishVersionEvent(PublishVersionEvent $event): void
    {
        $content = $event->getContent();
        $contentType = $content->getContentType();
        if ($this->isTranslationAware && $this->isLandingPage($contentType)) {
            $tags = [ContentTagInterface::CONTENT_PREFIX . $content->contentInfo->id];
            $this->purgeClient->purge($tags);
        }
    }

    private function isLandingPage(ContentType $contentType): bool
    {
        return $contentType->getFieldDefinitions()->any(static function (FieldDefinition $fieldDefinition): bool {
            return $fieldDefinition->fieldTypeIdentifier === Type::IDENTIFIER;
        });
    }
}
