<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Repository\Search\ElasticSearch;

use Ibexa\Contracts\Core\Persistence\Content as SPIContent;
use Ibexa\Contracts\Core\Persistence\Content\Handler as ContentHandler;
use Ibexa\Contracts\Core\Search\Document;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\ContentIndexCreateEvent;
use Ibexa\Contracts\Elasticsearch\Mapping\Event\LocationIndexCreateEvent;
use Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\IndexDataProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class IndexCreateSubscriber implements EventSubscriberInterface
{
    private ContentHandler $contentHandler;

    /** @var \Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\IndexDataProviderInterface[] */
    private iterable $indexDataProviders;

    /**
     * @param \Ibexa\ProductCatalog\Local\Repository\Search\Common\IndexDataProvider\IndexDataProviderInterface[] $indexDataProviders
     */
    public function __construct(ContentHandler $contentHandler, iterable $indexDataProviders)
    {
        $this->contentHandler = $contentHandler;
        $this->indexDataProviders = $indexDataProviders;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentIndexCreateEvent::class => 'onContentIndexCreate',
            LocationIndexCreateEvent::class => 'onLocationIndexCreate',
        ];
    }

    public function onContentIndexCreate(ContentIndexCreateEvent $event): void
    {
        foreach ($this->indexDataProviders as $indexDataProvider) {
            if (!$indexDataProvider->isSupported($event->getContent())) {
                continue;
            }

            $this->appendSearchData(
                $indexDataProvider,
                $event->getDocument(),
                $event->getContent()
            );
        }
    }

    public function onLocationIndexCreate(LocationIndexCreateEvent $event): void
    {
        $content = $this->contentHandler->load(
            $event->getLocation()->contentId
        );

        foreach ($this->indexDataProviders as $indexDataProvider) {
            if (!$indexDataProvider->isSupported($content)) {
                continue;
            }

            $this->appendSearchData(
                $indexDataProvider,
                $event->getDocument(),
                $content
            );
        }
    }

    private function appendSearchData(
        IndexDataProviderInterface $indexDataProvider,
        Document $document,
        SPIContent $content
    ): void {
        foreach ($indexDataProvider->getSearchData($content) as $field) {
            $document->fields[] = $field;
        }
    }
}
