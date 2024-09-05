<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Cache;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\LocationPathConverter;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\Personalization\Service\Model\ModelServiceInterface;
use Ibexa\Personalization\Value\Model\Attribute;
use Ibexa\Personalization\Value\Model\AttributeList;
use Ibexa\Personalization\Value\Model\EditorContentList;
use Ibexa\Personalization\Value\Model\Model;
use Ibexa\Personalization\Value\Model\ModelList;
use Ibexa\Personalization\Value\Model\ModelUpdateStruct;
use Ibexa\Personalization\Value\Model\SegmentsStruct;
use Ibexa\Personalization\Value\Model\SegmentsUpdateStruct;
use Ibexa\Personalization\Value\Model\Submodel;
use Ibexa\Personalization\Value\Model\SubmodelList;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class CachedModelService extends AbstractCacheServiceDecorator implements ModelServiceInterface
{
    private const SUBMODEL_KEY = 'recommendation-submodel';
    private const SUBMODELS_KEY = 'recommendation-submodels';
    private const ATTRIBUTE_KEY = 'recommendation-attribute';
    private const ATTRIBUTES_KEY = 'recommendation-attributes';
    private const EDITOR_LIST_KEY = 'recommendation-editor-list';

    /** @var \Ibexa\Personalization\Service\Model\ModelServiceInterface */
    private $innerModelService;

    public function __construct(
        TagAwareAdapterInterface $cache,
        PersistenceHandler $persistenceHandler,
        PersistenceLogger $logger,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        CacheIdentifierSanitizer $cacheIdentifierSanitizer,
        LocationPathConverter $locationPathConverter,
        ModelServiceInterface $innerModelService
    ) {
        parent::__construct(
            $cache,
            $persistenceHandler,
            $logger,
            $cacheIdentifierGenerator,
            $cacheIdentifierSanitizer,
            $locationPathConverter
        );

        $this->innerModelService = $innerModelService;
    }

    public function getModels(int $customerId): ModelList
    {
        return $this->innerModelService->getModels($customerId);
    }

    public function getModel(int $customerId, string $referenceCode): Model
    {
        return $this->innerModelService->getModel($customerId, $referenceCode);
    }

    public function getSubmodels(int $customerId, string $referenceCode): SubmodelList
    {
        $arguments = [
            self::SUBMODELS_KEY,
            $customerId,
            $referenceCode,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SUBMODELS_KEY, $arguments),
            function () use ($customerId, $referenceCode): SubmodelList {
                return $this->innerModelService->getSubmodels(
                    $customerId,
                    $referenceCode
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getSubmodel(int $customerId, string $referenceCode, string $attributeKey): Submodel
    {
        $arguments = [
            self::SUBMODEL_KEY,
            $customerId,
            $referenceCode,
            $attributeKey,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::SUBMODEL_KEY, $arguments),
            function () use ($customerId, $referenceCode, $attributeKey): Submodel {
                return $this->innerModelService->getSubmodel(
                    $customerId,
                    $referenceCode,
                    $attributeKey
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getAttributes(int $customerId): AttributeList
    {
        $arguments = [
            self::ATTRIBUTES_KEY,
            $customerId,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::ATTRIBUTES_KEY, $arguments),
            function () use ($customerId): AttributeList {
                return $this->innerModelService->getAttributes(
                    $customerId
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getAttribute(
        int $customerId,
        string $attributeKey,
        string $attributeType,
        ?string $attributeSource = null,
        ?string $source = null
    ): Attribute {
        $arguments = [
            self::ATTRIBUTE_KEY,
            $customerId,
            $attributeKey,
            $attributeType,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::ATTRIBUTE_KEY, $arguments),
            function () use ($customerId, $attributeKey, $attributeType, $attributeSource, $source): Attribute {
                return $this->innerModelService->getAttribute(
                    $customerId,
                    $attributeKey,
                    $attributeType,
                    $attributeSource,
                    $source
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }

    public function getSegments(
        int $customerId,
        string $referenceCode,
        ?string $maximumRatingAge,
        ?string $valueEventType
    ): ?SegmentsStruct {
        return $this->innerModelService->getSegments($customerId, $referenceCode, $maximumRatingAge, $valueEventType);
    }

    public function updateModel(int $customerId, Model $model, ModelUpdateStruct $modelUpdateStruct): void
    {
        $this->innerModelService->updateModel($customerId, $model, $modelUpdateStruct);

        $this->removeCacheItem(
            self::SUBMODELS_KEY,
            [
                self::SUBMODELS_KEY,
                $customerId,
                $model->getReferenceCode(),
            ]
        );

        $this->removeCacheItem(
            self::EDITOR_LIST_KEY,
            [
                self::EDITOR_LIST_KEY,
                $customerId,
                $model->getReferenceCode(),
            ]
        );
    }

    public function updateSegments(
        int $customerId,
        Model $model,
        SegmentsUpdateStruct $segmentsUpdateStruct
    ): void {
        $this->innerModelService->updateSegments($customerId, $model, $segmentsUpdateStruct);
    }

    public function getEditorContentList(int $customerId, string $referenceCode): EditorContentList
    {
        $arguments = [
            self::EDITOR_LIST_KEY,
            $customerId,
            $referenceCode,
        ];

        $item = $this->processCacheItem(
            $this->buildCacheKey($arguments),
            $this->buildCacheTagKeys(self::EDITOR_LIST_KEY, $arguments),
            function () use ($customerId, $referenceCode): EditorContentList {
                return $this->innerModelService->getEditorContentList(
                    $customerId,
                    $referenceCode
                );
            },
            parent::DEFAULT_EXPIRATION_TIME
        );

        return $item->get();
    }
}

class_alias(CachedModelService::class, 'Ibexa\Platform\Personalization\Cache\CachedModelService');
