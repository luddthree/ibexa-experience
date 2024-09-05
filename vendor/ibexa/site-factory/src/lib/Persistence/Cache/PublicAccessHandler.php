<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Cache;

use Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\SiteFactory\Persistence\PublicAccess\Handler;
use Ibexa\SiteFactory\Persistence\PublicAccess\Handler\HandlerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PublicAccessHandler extends AbstractHandler implements HandlerInterface
{
    /** @var \Ibexa\SiteFactory\Persistence\PublicAccess\Handler\PublicAccessHandler */
    private $publicAccessHandler;

    /** @var \Ibexa\Core\Persistence\Cache\PersistenceLogger */
    private $logger;

    /** @var \Symfony\Contracts\Cache\TagAwareCacheInterface */
    private $cachePool;

    public function __construct(
        TagAwareCacheInterface $cachePool,
        PersistenceLogger $logger,
        Handler\PublicAccessHandler $publicAccessHandler
    ) {
        $this->cachePool = $cachePool;
        $this->logger = $logger;
        $this->publicAccessHandler = $publicAccessHandler;
    }

    public function load(string $identifier): ?PublicAccess
    {
        /** @var \Symfony\Component\Cache\CacheItem $cacheItem */
        $cacheItem = $this->cachePool->getItem('ez-public-access-' . $identifier);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['publicAccess' => $identifier]);
        $publicAccess = $this->publicAccessHandler->load($identifier);
        $cacheItem->set($publicAccess);
        $tags = ['public-access-' . $identifier];
        if ($publicAccess !== null) {
            $tags[] = 'site-' . $publicAccess->getSiteId();
        }
        $cacheItem->tag($tags);
        $this->cachePool->save($cacheItem);

        return $publicAccess;
    }

    public function find(SiteQuery $query): array
    {
        $this->logger->logCall(__METHOD__, [
            'query' => $query,
        ]);

        return $this->publicAccessHandler->find($query);
    }

    public function match(string $host): array
    {
        $cacheKey = $this->escapeForCacheKey('ez-public-accesses-' . $host);
        /** @var \Symfony\Component\Cache\CacheItem $cacheItem */
        $cacheItem = $this->cachePool->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $this->logger->logCall(__METHOD__, ['host' => $host]);
        $matched = $this->publicAccessHandler->match($host);
        $cacheItem->set($matched);
        $tags = [$cacheKey];
        if (!empty($matched)) {
            foreach ($matched as $publicAccessData) {
                $tags[] = 'site-' . $publicAccessData['site_id'];
            }
        }
        $cacheItem->tag($tags);
        $this->cachePool->save($cacheItem);

        return $matched;
    }
}

class_alias(PublicAccessHandler::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Cache\PublicAccessHandler');
