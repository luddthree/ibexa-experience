<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Cache\AttributeGroup;

use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeGroup\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroup;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeGroupUpdateStruct;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class Handler implements HandlerInterface
{
    private const ATTRIBUTE_GROUP_IDENTIFIER = 'attribute_group';

    private HandlerInterface $innerHandler;

    private TagAwareAdapterInterface $cache;

    private PersistenceLogger $logger;

    private CacheIdentifierGeneratorInterface $cacheIdentifierGenerator;

    private CacheIdentifierSanitizer $cacheIdentifierSanitizer;

    public function __construct(
        HandlerInterface $innerHandler,
        PersistenceLogger $logger,
        TagAwareAdapterInterface $cache,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer
    ) {
        $this->innerHandler = $innerHandler;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
        $this->cacheIdentifierSanitizer = $cacheIdentifierSanitizer;
    }

    public function load(int $id): AttributeGroup
    {
        $cacheItem = $this->cache->getItem(
            $this->cacheIdentifierGenerator->generateKey(self::ATTRIBUTE_GROUP_IDENTIFIER, [$id], true)
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['attributeGroup' => $id]);
        $attributeGroup = $this->innerHandler->load($id);
        $cacheItem->set($attributeGroup);
        $cacheItem->tag(
            [
                $this->cacheIdentifierGenerator->generateTag(self::ATTRIBUTE_GROUP_IDENTIFIER, [$attributeGroup->id]),
                $this->cacheIdentifierGenerator->generateTag(
                    self::ATTRIBUTE_GROUP_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($attributeGroup->identifier)]
                ),
            ],
        );
        $this->cache->save($cacheItem);

        return $attributeGroup;
    }

    public function loadByIdentifier(string $identifier): AttributeGroup
    {
        $cacheItem = $this->cache->getItem(
            $this->cacheIdentifierGenerator->generateKey(
                self::ATTRIBUTE_GROUP_IDENTIFIER,
                [$this->cacheIdentifierSanitizer->escapeForCacheKey($identifier)],
                true
            )
        );
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['attributeGroup' => $identifier]);
        $attributeGroup = $this->innerHandler->loadByIdentifier($identifier);
        $cacheItem->set($attributeGroup);
        $cacheItem->tag(
            [
                $this->cacheIdentifierGenerator->generateTag(self::ATTRIBUTE_GROUP_IDENTIFIER, [$attributeGroup->id]),
                $this->cacheIdentifierGenerator->generateTag(
                    self::ATTRIBUTE_GROUP_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($attributeGroup->identifier)]
                ),
            ]
        );
        $this->cache->save($cacheItem);

        return $attributeGroup;
    }

    public function findMatching(?string $namePrefix, int $offset, int $limit): array
    {
        $this->logger->logCall(__METHOD__, [
            'namePrefix' => $namePrefix,
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $this->innerHandler->findMatching($namePrefix, $offset, $limit);
    }

    public function countMatching(?string $namePrefix): int
    {
        $this->logger->logCall(__METHOD__, [
            'namePrefix' => $namePrefix,
        ]);

        return $this->innerHandler->countMatching($namePrefix);
    }

    public function create(AttributeGroupCreateStruct $createStruct): void
    {
        $this->logger->logCall(__METHOD__, [
            'createStruct' => $createStruct,
        ]);

        $this->innerHandler->create($createStruct);
    }

    public function update(AttributeGroupUpdateStruct $updateStruct): void
    {
        $this->logger->logCall(__METHOD__, [
            'updateStruct' => $updateStruct,
        ]);

        $this->innerHandler->update($updateStruct);

        $invalidateTags = [
            $this->cacheIdentifierGenerator->generateTag(self::ATTRIBUTE_GROUP_IDENTIFIER, [$updateStruct->id]),
        ];

        $this->cache->invalidateTags($invalidateTags);
    }

    public function deleteByIdentifier(string $identifier): void
    {
        $this->logger->logCall(__METHOD__, [
            'identifier' => $identifier,
        ]);

        $this->innerHandler->deleteByIdentifier($identifier);

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(
                    self::ATTRIBUTE_GROUP_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($identifier)]
                ),
            ]
        );
    }

    public function deleteTranslation(string $identifier, int $languageId): void
    {
        $this->logger->logCall(__METHOD__, [
            'identifier' => $identifier,
            'languageId' => $languageId,
        ]);

        $this->innerHandler->deleteTranslation($identifier, $languageId);

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(
                    self::ATTRIBUTE_GROUP_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($identifier)]
                ),
            ]
        );
    }
}
