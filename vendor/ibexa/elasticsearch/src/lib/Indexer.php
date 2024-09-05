<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch;

use AppendIterator;
use Doctrine\DBAL\Connection;
use Exception;
use Ibexa\Contracts\Core\Persistence\Content\ContentInfo;
use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Search\Handler as SearchHandler;
use Ibexa\Core\Search\Common\IncrementalIndexer;
use Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface as DocumentFactoryInterface;
use Psr\Log\LoggerInterface;

class Indexer extends IncrementalIndexer
{
    /** @var \Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface */
    private $documentFactory;

    public function __construct(
        LoggerInterface $logger,
        PersistenceHandler $persistenceHandler,
        Connection $connection,
        SearchHandler $searchHandler,
        DocumentFactoryInterface $documentFactory
    ) {
        parent::__construct($logger, $persistenceHandler, $connection, $searchHandler);
        $this->documentFactory = $documentFactory;
    }

    public function updateSearchIndex(array $contentIds, $commit): void
    {
        $contentHandler = $this->persistenceHandler->contentHandler();
        $locationHandler = $this->persistenceHandler->locationHandler();

        $documents = new AppendIterator();
        foreach ($contentIds as $contentId) {
            try {
                $contentInfo = $contentHandler->loadContentInfo($contentId);
                if ($contentInfo->status === ContentInfo::STATUS_PUBLISHED) {
                    $content = $contentHandler->load($contentId);
                    $locations = $locationHandler->loadLocationsByContent($contentId);

                    $documents->append($this->documentFactory->fromContent($content));
                    foreach ($locations as $location) {
                        $documents->append($this->documentFactory->fromLocation($location, $content));
                    }
                }
            } catch (NotFoundException $e) {
                $this->searchHandler->deleteContent($contentId);
            } catch (Exception $e) {
                $this->logger->error('Unable to index the content', [
                    'contentId' => $contentId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->searchHandler->addDocuments($documents);
    }

    public function purge(): void
    {
        $this->searchHandler->purgeIndex();
    }

    public function getName(): string
    {
        return 'Ibexa Elasticsearch Search Engine';
    }
}

class_alias(Indexer::class, 'Ibexa\Platform\ElasticSearchEngine\Indexer');
