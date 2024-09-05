<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Storage;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\Personalization\Value\ItemInterface;
use Ibexa\Core\QueryType\QueryType;
use Ibexa\Core\Repository\Repository;
use Ibexa\Personalization\Content\DataResolverInterface;
use Ibexa\Personalization\Exception\ItemNotFoundException;
use Ibexa\Personalization\Value\Storage\Item;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\Personalization\Value\Storage\ItemType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ContentDataSource implements DataSourceInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private ContentService $contentService;

    private DataResolverInterface $dataResolver;

    private QueryType $queryType;

    private Repository $repository;

    private SearchService $searchService;

    public function __construct(
        ContentService $contentService,
        DataResolverInterface $dataResolver,
        QueryType $queryType,
        Repository $repository,
        SearchService $searchService,
        ?LoggerInterface $logger = null
    ) {
        $this->contentService = $contentService;
        $this->dataResolver = $dataResolver;
        $this->queryType = $queryType;
        $this->repository = $repository;
        $this->searchService = $searchService;
        $this->logger = $logger ?? new NullLogger();
    }

    public function countItems(CriteriaInterface $criteria): int
    {
        try {
            $query = $this->queryType->getQuery(['criteria' => $criteria]);
            $query->limit = 0;
            $languageFilter = $this->getLanguageFilter($criteria->getLanguages());

            return $this->searchService->findContent($query, $languageFilter)->totalCount ?? 0;
        } catch (NotFoundException | InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage());

            return 0;
        }
    }

    public function fetchItems(CriteriaInterface $criteria): iterable
    {
        try {
            $languages = $criteria->getLanguages();
            $languageFilter = $this->getLanguageFilter($criteria->getLanguages());
            $items = [];

            /**
             * @var \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchHit $hit
             */
            foreach ($this->searchService->findContent($this->getQuery($criteria), $languageFilter) as $hit) {
                /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
                $content = $hit->valueObject;
                $languageCodes = array_intersect(
                    $this->getLanguageCodesFromVersionInfo($content->getVersionInfo()),
                    $languages
                );

                foreach ($languageCodes as $languageCode) {
                    $item = $this->createItem($content, $languageCode);
                    $items[$item->getId() . $item->getLanguage()] = $item;
                }
            }

            return new ItemList($items);
        } catch (NotFoundException | InvalidArgumentException $exception) {
            $this->logger->error($exception->getMessage());

            return new ItemList([]);
        }
    }

    public function fetchItem(string $id, string $language): ItemInterface
    {
        try {
            return $this->repository->sudo(
                fn (): ItemInterface => $this->createItem(
                    $this->contentService->loadContent((int) $id, [$language]),
                    $language
                )
            );
        } catch (NotFoundException $exception) {
            throw new ItemNotFoundException($id, $language, 0, $exception);
        }
    }

    private function getQuery(CriteriaInterface $criteria): Query
    {
        $query = $this->queryType->getQuery(['criteria' => $criteria]);
        $query->performCount = false;
        $query->limit = $criteria->getLimit();
        $query->offset = $criteria->getOffset();

        return $query;
    }

    /**
     * @return array<string>
     */
    private function getLanguageCodesFromVersionInfo(VersionInfo $versionInfo): array
    {
        $languageCodes = [];
        foreach ($versionInfo->getLanguages() as $language) {
            $languageCodes[] = $language->getLanguageCode();
        }

        return $languageCodes;
    }

    private function createItem(
        Content $content,
        string $languageCode
    ): ItemInterface {
        return new Item(
            (string)$content->getVersionInfo()->getContentInfo()->getId(),
            ItemType::fromContentType($content->getContentType()),
            $languageCode,
            $this->dataResolver->resolve($content, $languageCode)
        );
    }

    public static function getDefaultPriority(): int
    {
        return 0;
    }

    /**
     * @param array<string> $languageCodes
     *
     * @return array{
     *     languages: array<string>,
     *     useAlwaysAvailable: bool,
     * }
     */
    private function getLanguageFilter(array $languageCodes): array
    {
        return [
            'languages' => $languageCodes,
            'useAlwaysAvailable' => false,
        ];
    }
}
