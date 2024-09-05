<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\Search\ElasticSearch;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\ContentIndexCreateEvent;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\LocationIndexCreateEvent;
use Ibexa\Taxonomy\Search\SearchIndexDataProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class IndexCreateSubscriber implements EventSubscriberInterface
{
    private SearchIndexDataProvider $searchIndexDataProvider;

    private ContentHandler $contentHandler;

    public function __construct(SearchIndexDataProvider $searchIndexDataProvider, ContentHandler $contentHandler)
    {
        $this->searchIndexDataProvider = $searchIndexDataProvider;
        $this->contentHandler = $contentHandler;
    }

    /**
     * @return array<string,string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ContentIndexCreateEvent::class => 'onContentIndexCreate',
            LocationIndexCreateEvent::class => 'onLocationIndexCreate',
        ];
    }

    public function onContentIndexCreate(ContentIndexCreateEvent $event): void
    {
        $this->appendSearchData($event->getDocument(), $event->getContent());
    }

    public function onLocationIndexCreate(LocationIndexCreateEvent $event): void
    {
        $content = $this->contentHandler->load(
            $event->getLocation()->contentId
        );

        $this->appendSearchData($event->getDocument(), $content);
    }

    private function appendSearchData(Document $document, SPIContent $content): void
    {
        $fields = $this->searchIndexDataProvider->getSearchData($content);
        foreach ($fields as $field) {
            $document->fields[] = $field;
        }
    }
}
