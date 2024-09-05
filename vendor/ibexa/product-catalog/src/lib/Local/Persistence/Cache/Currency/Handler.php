<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Local\Persistence\Cache\Currency;

use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\ProductCatalog\Local\Persistence\Legacy\Currency\HandlerInterface;
use Ibexa\ProductCatalog\Local\Persistence\Values\Currency;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyCreateStruct;
use Ibexa\ProductCatalog\Local\Persistence\Values\CurrencyUpdateStruct;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

final class Handler implements HandlerInterface
{
    private const CURRENCY_IDENTIFIER = 'currency';

    private HandlerInterface $innerHandler;

    private TagAwareAdapterInterface $cache;

    private PersistenceLogger $logger;

    private CacheIdentifierGeneratorInterface $cacheIdentifierGenerator;

    public function __construct(
        HandlerInterface $innerHandler,
        PersistenceLogger $logger,
        TagAwareAdapterInterface $cache,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator
    ) {
        $this->innerHandler = $innerHandler;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
    }

    public function find(int $id): object
    {
        $cacheItem = $this->cache->getItem(
            $this->getKey($id)
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['currency' => $id]);
        $currency = $this->innerHandler->find($id);
        $cacheItem->set($currency);
        $cacheItem->tag(
            [
                $this->cacheIdentifierGenerator->generateTag(self::CURRENCY_IDENTIFIER, [$currency->id]),
            ],
        );
        $this->cache->save($cacheItem);

        return $currency;
    }

    public function create(CurrencyCreateStruct $struct): Currency
    {
        $this->logger->logCall(__METHOD__, [
            'createStruct' => $struct,
        ]);

        return $this->innerHandler->create($struct);
    }

    public function update(CurrencyUpdateStruct $struct): Currency
    {
        $this->logger->logCall(__METHOD__, [
            'updateStruct' => $struct,
        ]);

        $currency = $this->innerHandler->update($struct);

        $this->cache->invalidateTags(
            [
                $this->cacheIdentifierGenerator->generateTag(self::CURRENCY_IDENTIFIER, [$struct->id]),
            ]
        );

        return $currency;
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
                $this->cacheIdentifierGenerator->generateTag(self::CURRENCY_IDENTIFIER, [$id]),
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

    private function getKey(int $id): string
    {
        return $this->cacheIdentifierGenerator->generateKey(
            self::CURRENCY_IDENTIFIER,
            [$id],
            true
        );
    }
}
