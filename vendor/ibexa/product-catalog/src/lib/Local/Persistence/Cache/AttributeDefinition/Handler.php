<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Cache\AttributeDefinition;

use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Persistence\Content\Type\Handler as ContentTypeHandler;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\AttributeDefinition\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinition;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\AttributeDefinitionUpdateStruct;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class Handler implements HandlerInterface
{
    private const ATTRIBUTE_DEFINITION_IDENTIFIER = 'attribute_definition';
    private const CONTENT_FIELDS_TYPE_IDENTIFIER = 'content_fields_type';
    private const TYPE_IDENTIFIER = 'type';
    private const TYPE_MAP_IDENTIFIER = 'type_map';
    private const PRODUCT_SPECIFICATION_FIELD_TYPE_IDENTIFIER = 'ibexa_product_specification';

    private HandlerInterface $innerHandler;

    private ContentTypeHandler $contentTypeHandler;

    private TagAwareAdapterInterface $cache;

    private CacheIdentifierGeneratorInterface $cacheIdentifierGenerator;

    private PersistenceLogger $logger;

    private CacheIdentifierSanitizer $cacheIdentifierSanitizer;

    public function __construct(
        HandlerInterface $innerHandler,
        ContentTypeHandler $contentTypeHandler,
        TagAwareAdapterInterface $cache,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        PersistenceLogger $logger,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer
    ) {
        $this->innerHandler = $innerHandler;
        $this->contentTypeHandler = $contentTypeHandler;
        $this->cache = $cache;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
        $this->logger = $logger;
        $this->cacheIdentifierSanitizer = $cacheIdentifierSanitizer;
    }

    public function load(int $id): AttributeDefinition
    {
        $cacheItem = $this->cache->getItem(
            $this->cacheIdentifierGenerator->generateKey(self::ATTRIBUTE_DEFINITION_IDENTIFIER, [$id], true)
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['attributeDefinition' => $id]);
        $attributeDefinition = $this->innerHandler->load($id);
        $cacheItem->set($attributeDefinition);
        $cacheItem->tag(
            [
                $this->cacheIdentifierGenerator->generateTag(self::ATTRIBUTE_DEFINITION_IDENTIFIER, [$attributeDefinition->id]),
                $this->cacheIdentifierGenerator->generateTag(
                    self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($attributeDefinition->identifier)]
                ),
            ],
        );
        $this->cache->save($cacheItem);

        return $attributeDefinition;
    }

    public function loadByIdentifier(string $identifier): AttributeDefinition
    {
        $cacheItem = $this->cache->getItem(
            $this->cacheIdentifierGenerator->generateKey(
                self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                [$this->cacheIdentifierSanitizer->escapeForCacheKey($identifier)],
                true
            )
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['attributeDefinition' => $identifier]);
        $attributeDefinition = $this->innerHandler->loadByIdentifier($identifier);
        $cacheItem->set($attributeDefinition);
        $cacheItem->tag(
            [
                $this->cacheIdentifierGenerator->generateTag(self::ATTRIBUTE_DEFINITION_IDENTIFIER, [$attributeDefinition->id]),
                $this->cacheIdentifierGenerator->generateTag(
                    self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($attributeDefinition->identifier)]
                ),
            ]
        );
        $this->cache->save($cacheItem);

        return $attributeDefinition;
    }

    public function findMatching(AttributeDefinitionQuery $query): array
    {
        $offset = $query->getOffset();
        $limit = $query->getLimit();

        $this->logger->logCall(__METHOD__, [
            'offset' => $offset,
            'limit' => $limit,
        ]);

        return $this->innerHandler->findMatching($query);
    }

    public function countMatching(AttributeDefinitionQuery $query): int
    {
        $this->logger->logCall(__METHOD__);

        return $this->innerHandler->countMatching($query);
    }

    public function create(AttributeDefinitionCreateStruct $createStruct): void
    {
        $this->logger->logCall(__METHOD__, [
            'createStruct' => $createStruct,
        ]);

        $this->innerHandler->create($createStruct);
    }

    public function update(AttributeDefinitionUpdateStruct $updateStruct): void
    {
        $this->logger->logCall(__METHOD__, [
            'updateStruct' => $updateStruct,
        ]);

        $existingAttributeDefinitionIdentifier = $this->innerHandler->load($updateStruct->id)->identifier;
        $this->innerHandler->update($updateStruct);

        $tags = [];
        $contentTypeIds = $this->fetchContentTypeIdsContainingAttributeDefinition(
            $existingAttributeDefinitionIdentifier
        );

        foreach ($contentTypeIds as $id) {
            $tags[] = $this->cacheIdentifierGenerator->generateTag(self::CONTENT_FIELDS_TYPE_IDENTIFIER, [$id]);
            $tags[] = $this->cacheIdentifierGenerator->generateTag(self::TYPE_IDENTIFIER, [$id]);
        }

        $tags = array_merge(
            $tags,
            [
                $this->cacheIdentifierGenerator->generateTag(self::ATTRIBUTE_DEFINITION_IDENTIFIER, [$updateStruct->id]),
                $this->cacheIdentifierGenerator->generateTag(self::TYPE_MAP_IDENTIFIER),
            ]
        );

        $this->cache->invalidateTags($tags);
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
                    self::ATTRIBUTE_DEFINITION_IDENTIFIER,
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
                    self::ATTRIBUTE_DEFINITION_IDENTIFIER,
                    [$this->cacheIdentifierSanitizer->escapeForCacheKey($identifier)]
                ),
            ]
        );
    }

    /**
     * @return int[]
     */
    private function fetchContentTypeIdsContainingAttributeDefinition(string $attributeIdentifier): array
    {
        $contentTypes = $this->contentTypeHandler->loadContentTypesByFieldDefinitionIdentifier(
            self::PRODUCT_SPECIFICATION_FIELD_TYPE_IDENTIFIER
        );

        $contentTypeIds = [];
        foreach ($contentTypes as $contentType) {
            foreach ($contentType->fieldDefinitions as $definition) {
                if ($this->isAttributeAssignedToProductType($definition, $attributeIdentifier)) {
                    $contentTypeIds[] = $contentType->id;

                    break;
                }
            }
        }

        return $contentTypeIds;
    }

    private function isAttributeAssignedToProductType(
        FieldDefinition $fieldDefinition,
        string $attributeIdentifier
    ): bool {
        $attributeDefinitions = $fieldDefinition->fieldTypeConstraints->fieldSettings['attributes_definitions'] ?? [];
        foreach ($attributeDefinitions as $definitionArray) {
            foreach ($definitionArray as $definition) {
                if ($definition['attributeDefinition'] === $attributeIdentifier) {
                    return true;
                }
            }
        }

        return false;
    }
}
