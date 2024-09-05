<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\Page\Storage\Handler\Cache;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\AbstractInMemoryPersistenceHandler;
use Ibexa\Core\Persistence\Cache\Adapter\TransactionAwareAdapterInterface;
use Ibexa\Core\Persistence\Cache\CacheIndicesValidatorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\InMemory\InMemoryCache;
use Ibexa\Core\Persistence\Cache\LocationPathConverter;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\FieldTypePage\FieldType\Page\Storage\Handler\PageHandlerInterface;

class PageHandler extends AbstractInMemoryPersistenceHandler implements PageHandlerInterface
{
    /** @var \Ibexa\FieldTypePage\FieldType\Page\Storage\Handler\PageHandlerInterface */
    private $pageHandler;

    public function __construct(
        TransactionAwareAdapterInterface $cache,
        PersistenceLogger $logger,
        InMemoryCache $inMemory,
        PersistenceHandler $persistenceHandler,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        PageHandlerInterface $pageHandler,
        ?CacheIndicesValidatorInterface $cacheIndicesValidator = null
    ) {
        parent::__construct(
            $cache,
            $logger,
            $inMemory,
            $persistenceHandler,
            $cacheIdentifierGenerator,
            $cacheIdentifierSanitizer,
            $locationPathConverter,
            $cacheIndicesValidator
        );

        $this->pageHandler = $pageHandler;
    }

    public function loadPageByContentId(int $contentId, int $versionNo, string $languageCode): array
    {
        return $this->getCacheValue(
            implode('|', [$contentId, $versionNo, $languageCode]),
            'page-',
            function (string $combinedKey): array {
                [$contentId, $versionNo, $languageCode] = explode('|', $combinedKey);
                $this->logger->logCall(
                    __METHOD__,
                    [
                        'contentId' => $contentId,
                        'versionNo' => $versionNo,
                        'languageCode' => $languageCode,
                    ]
                );

                return $this->pageHandler->loadPageByContentId((int) $contentId, (int) $versionNo, $languageCode);
            },
            static function (array $page) use ($contentId, $versionNo, $languageCode): array {
                return [
                    sprintf('page-%d-%d-%s', $contentId, $versionNo, $languageCode),
                    sprintf('page-%d', $page['id']),
                    sprintf('content-%d', $contentId),
                    sprintf('content-%d-version-%d', $contentId, $versionNo),
                ];
            },
            static function () use ($contentId, $versionNo, $languageCode) {
                return [
                    sprintf('page-%d-%d-%s', $contentId, $versionNo, $languageCode),
                ];
            }
        );
    }

    public function loadPagesMappedToContent(int $contentId, int $versionNo, array $languageCodes): array
    {
        $this->logger->logCall(__METHOD__, [
            'contentId' => $contentId,
            'versionNo' => $versionNo,
            'languageCodes' => $languageCodes,
        ]);

        return $this->pageHandler->loadPagesMappedToContent($contentId, $versionNo, $languageCodes);
    }

    public function insertPage(int $contentId, int $versionNo, string $languageCode, array $page): int
    {
        $this->logger->logCall(__METHOD__, [
            'contentId' => $contentId,
            'versionNo' => $versionNo,
            'languageCode' => $languageCode,
        ]);

        $pageId = $this->pageHandler->insertPage(
            $contentId,
            $versionNo,
            $languageCode,
            $page
        );

        $this->cache->invalidateTags(["content-{$contentId}-version-{$versionNo}"]);

        return $pageId;
    }

    public function removePage(int $pageId): void
    {
        $this->logger->logCall(__METHOD__, [
            'id' => $pageId,
        ]);

        $this->pageHandler->removePage($pageId);

        $this->cache->invalidateTags(['page-' . $pageId]);
    }
}
