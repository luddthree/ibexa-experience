<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Elasticsearch;

use Elasticsearch\Client;
use Ibexa\Contracts\Core\Persistence\Content;
use Ibexa\Contracts\Core\Persistence\Content\Location;
use Ibexa\Contracts\Core\Repository\Exceptions\NotImplementedException;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationQuery;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Search\VersatileHandler;
use Ibexa\Contracts\Elasticsearch\Mapping\ContentDocument;
use Ibexa\Contracts\Elasticsearch\Mapping\LocationDocument;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\Base\Exceptions\NotFoundException;
use Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface;
use Ibexa\Elasticsearch\DocumentSerializer\DocumentSerializerInterface;
use Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface;
use Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface;
use Ibexa\Elasticsearch\Query\CoordinatorInterface;

class Handler implements VersatileHandler
{
    /** @var \Ibexa\Elasticsearch\ElasticSearch\Client\ClientFactoryInterface */
    private $clientFactory;

    /** @var \Ibexa\Elasticsearch\Query\CoordinatorInterface */
    private $contentQueryCoordinator;

    /** @var \Ibexa\Elasticsearch\Query\CoordinatorInterface */
    private $locationQueryCoordinator;

    /** @var \Ibexa\Elasticsearch\DocumentMapper\DocumentFactoryInterface */
    private $documentFactory;

    /** @var \Ibexa\Elasticsearch\DocumentSerializer\DocumentSerializerInterface */
    private $documentSerializer;

    /** @var \Ibexa\Elasticsearch\ElasticSearch\Index\IndexResolverInterface */
    private $indexResolver;

    /** @var \Elasticsearch\Client */
    private $client;

    public function __construct(
        ClientFactoryInterface $clientFactory,
        CoordinatorInterface $contentQueryCoordinator,
        CoordinatorInterface $locationQueryCoordinator,
        IndexResolverInterface $indexResolver,
        DocumentFactoryInterface $documentFactory,
        DocumentSerializerInterface $documentSerializer
    ) {
        $this->clientFactory = $clientFactory;
        $this->contentQueryCoordinator = $contentQueryCoordinator;
        $this->locationQueryCoordinator = $locationQueryCoordinator;
        $this->indexResolver = $indexResolver;
        $this->documentFactory = $documentFactory;
        $this->documentSerializer = $documentSerializer;
    }

    public function supports(int $capabilityFlag): bool
    {
        switch ($capabilityFlag) {
            case SearchService::CAPABILITY_SCORING:
            case SearchService::CAPABILITY_FACETS:
            case SearchService::CAPABILITY_CUSTOM_FIELDS:
            case SearchService::CAPABILITY_SPELLCHECK:
            case SearchService::CAPABILITY_ADVANCED_FULLTEXT:
            case SearchService::CAPABILITY_AGGREGATIONS:
                return true;
            default:
                return false;
        }
    }

    public function findContent(Query $query, array $languageFilter = [])
    {
        return $this->contentQueryCoordinator->execute(
            $this->getClient(),
            $query,
            $languageFilter
        );
    }

    public function findSingle(Criterion $filter, array $languageFilter = [])
    {
        $query = new Query();
        $query->filter = $filter;
        $query->query = new Criterion\MatchAll();
        $query->offset = 0;
        $query->limit = 1;

        $result = $this->contentQueryCoordinator->execute(
            $this->getClient(),
            $query,
            $languageFilter
        );

        if ($result->totalCount < 1) {
            throw new NotFoundException('Content', 'findSingle() found no content for the given $filter');
        }

        if ($result->totalCount > 1) {
            throw new InvalidArgumentException('totalCount', 'findSingle() found more then one Content item for the given $filter');
        }

        return reset($result->searchHits)->valueObject;
    }

    public function findLocations(LocationQuery $query, array $languageFilter = [])
    {
        return $this->locationQueryCoordinator->execute(
            $this->getClient(),
            $query,
            $languageFilter
        );
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, Criterion $filter = null)
    {
        throw new NotImplementedException('Suggestions are not supported by elasticsearch search engine.');
    }

    /**
     * @param \Ibexa\Contracts\Elasticsearch\Mapping\BaseDocument[] $documents
     */
    public function addDocuments(iterable $documents): void
    {
        $request = [
            'body' => [],
        ];

        foreach ($documents as $document) {
            $request['body'][] = [
                'index' => [
                    '_id' => $document->id,
                    '_index' => $this->indexResolver->getIndexNameForDocument($document),
                ],
            ];

            $request['body'][] = $this->documentSerializer->serialize($document);
        }

        if (!empty($request['body'])) {
            $this->getClient()->bulk($request);
            $this->getClient()->indices()->refresh([
                'index' => '_all',
            ]);
        }
    }

    public function indexContent(Content $content): void
    {
        $this->addDocuments($this->documentFactory->fromContent($content));
    }

    public function indexLocation(Location $location): void
    {
        $this->addDocuments($this->documentFactory->fromLocation($location));
    }

    public function deleteContent($contentId, $versionId = null): void
    {
        $this->getClient()->deleteByQuery([
            'index' => $this->getAllIndicesWildcard(),
            'body' => [
                'query' => [
                    'term' => [
                        'content_id_id' => $contentId,
                    ],
                ],
            ],
            'conflicts' => 'proceed',
            'refresh' => true,
        ]);

        $this->getClient()->indices()->refresh([
            'index' => '_all',
        ]);
    }

    public function deleteLocation($locationId, $contentId): void
    {
        $this->getClient()->deleteByQuery([
            'index' => $this->indexResolver->getIndexWildcard(LocationDocument::class),
            'body' => [
                'query' => [
                    'term' => [
                        'location_id_id' => $locationId,
                    ],
                ],
            ],
            'conflicts' => 'proceed',
            'refresh' => true,
        ]);

        $this->getClient()->indices()->refresh([
            'index' => '_all',
        ]);
    }

    public function deleteTranslation(int $contentId, string $languageCode): void
    {
        $this->getClient()->deleteByQuery([
            'index' => $this->getAllIndicesWildcard(),
            'body' => [
                'query' => [
                    'bool' => [
                        'filter' => [
                            [
                                'term' => [
                                    'content_id_id' => $contentId,
                                ],
                            ],
                            [
                                'term' => [
                                    'meta_indexed_language_code_s' => $languageCode,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->getClient()->indices()->refresh([
            'index' => '_all',
        ]);
    }

    public function purgeIndex(): void
    {
        $this->getClient()->deleteByQuery([
            'index' => $this->getAllIndicesWildcard(),
            'body' => [
                'query' => [
                    'match_all' => [
                        'boost' => 1.0,
                    ],
                ],
            ],
        ]);
    }

    private function getAllIndicesWildcard(): string
    {
        return implode(',', [
            $this->indexResolver->getIndexWildcard(ContentDocument::class),
            $this->indexResolver->getIndexWildcard(LocationDocument::class),
        ]);
    }

    private function getClient(): Client
    {
        if ($this->client === null) {
            $this->client = $this->clientFactory->create();
        }

        return $this->client;
    }
}

class_alias(Handler::class, 'Ibexa\Platform\ElasticSearchEngine\Handler');
