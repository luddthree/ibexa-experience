<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Cache\CustomerGroup;

use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupCreateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroup\CustomerGroupUpdateStruct;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\CustomerGroup\HandlerInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class Handler implements HandlerInterface
{
    private const CUSTOMER_GROUP_IDENTIFIER = 'customer_group';

    private HandlerInterface $innerHandler;

    private TagAwareAdapterInterface $cache;

    private CacheIdentifierGeneratorInterface $cacheIdentifierGenerator;

    private PersistenceLogger $logger;

    public function __construct(
        HandlerInterface $innerHandler,
        TagAwareAdapterInterface $cache,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        PersistenceLogger $logger
    ) {
        $this->innerHandler = $innerHandler;
        $this->cache = $cache;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
        $this->logger = $logger;
    }

    public function find(int $id): object
    {
        $cacheItem = $this->cache->getItem(
            $this->getKey($id)
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['customerGroup' => $id]);
        /** @var \Ibexa\ProductCatalog\Local\Persistence\Values\CustomerGroup $customerGroup */
        $customerGroup = $this->innerHandler->find($id);
        $cacheItem->set($customerGroup);
        $cacheItem->tag(
            [
                $this->cacheIdentifierGenerator->generateTag(self::CUSTOMER_GROUP_IDENTIFIER, [$customerGroup->id]),
            ],
        );
        $this->cache->save($cacheItem);

        return $customerGroup;
    }

    public function create(CustomerGroupCreateStruct $struct): int
    {
        $this->logger->logCall(__METHOD__, [
            'createStruct' => $struct,
        ]);

        return $this->innerHandler->create($struct);
    }

    public function update(CustomerGroupUpdateStruct $struct): void
    {
        $this->logger->logCall(__METHOD__, [
            'updateStruct' => $struct,
        ]);

        $this->innerHandler->update($struct);

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(self::CUSTOMER_GROUP_IDENTIFIER, [$struct->getId()]),
            ]
        );
    }

    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, int $offset = 0): array
    {
        $this->logger->logCall(__METHOD__, [
            'criteria' => $criteria,
            'orderBy' => $orderBy,
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return $this->innerHandler->findBy($criteria, $orderBy, $limit, $offset);
    }

    public function exists(int $id): bool
    {
        $this->logger->logCall(__METHOD__, [
            'id' => $id,
        ]);

        return $this->innerHandler->exists($id);
    }

    public function delete(int $id): void
    {
        $this->logger->logCall(__METHOD__, [
            'id' => $id,
        ]);

        $this->innerHandler->delete($id);

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(self::CUSTOMER_GROUP_IDENTIFIER, [$id]),
            ]
        );
    }

    public function deleteTranslation(CustomerGroupInterface $customerGroup, string $languageCode): void
    {
        $this->innerHandler->deleteTranslation($customerGroup, $languageCode);

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(
                    self::CUSTOMER_GROUP_IDENTIFIER,
                    [$customerGroup->getId()],
                ),
            ]
        );
    }

    public function findAll(?int $limit = null, int $offset = 0): array
    {
        $this->logger->logCall(__METHOD__, [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return $this->innerHandler->findAll($limit, $offset);
    }

    private function getKey(int $id): string
    {
        return $this->cacheIdentifierGenerator->generateKey(
            self::CUSTOMER_GROUP_IDENTIFIER,
            [$id],
            true
        );
    }

    public function countAll(): int
    {
        $this->logger->logCall(__METHOD__);

        return $this->innerHandler->countAll();
    }

    public function countBy(array $criteria): int
    {
        $this->logger->logCall(__METHOD__);

        return $this->innerHandler->countBy($criteria);
    }
}
