<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Persistence\Cache;

use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteCreateStruct;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteUpdateStruct;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use Ibexa\SiteFactory\Persistence\Site\Handler;
use Ibexa\SiteFactory\Persistence\Site\Handler\HandlerInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class SiteHandler extends AbstractHandler implements HandlerInterface
{
    /** @var \Ibexa\Core\Persistence\Cache\PersistenceLogger */
    private $logger;

    /** @var \Symfony\Contracts\Cache\TagAwareCacheInterface */
    private $cachePool;

    /** @var \Ibexa\SiteFactory\Persistence\Site\Handler\SiteHandler */
    private $siteHandler;

    public function __construct(
        TagAwareCacheInterface $cachePool,
        PersistenceLogger $logger,
        Handler\SiteHandler $siteHandler
    ) {
        $this->siteHandler = $siteHandler;
        $this->cachePool = $cachePool;
        $this->logger = $logger;
    }

    public function find(SiteQuery $query): array
    {
        $this->logger->logCall(__METHOD__, [
            'query' => $query,
        ]);

        return $this->siteHandler->find($query);
    }

    public function count(SiteQuery $query): int
    {
        $this->logger->logCall(__METHOD__, [
            'query' => $query,
        ]);

        return $this->siteHandler->count($query);
    }

    public function create(SiteCreateStruct $siteCreateStruct): Site
    {
        $this->logger->logCall(__METHOD__, [
            'siteCreateStruct' => $siteCreateStruct,
        ]);

        $site = $this->siteHandler->create($siteCreateStruct);

        $tags = [];
        /** @var \EzSystems\EzPlatformSiteFactory\Values\Site\PublicAccess $publicAccess */
        foreach ($siteCreateStruct->publicAccesses as $publicAccess) {
            $tags[] = 'ez-public-accesses-' . $this->escapeForCacheKey($publicAccess->getMatcherConfiguration()->host);
        }

        $this->cachePool->invalidateTags($tags);

        return $site;
    }

    public function update(int $siteId, SiteUpdateStruct $siteUpdateStruct): void
    {
        $this->logger->logCall(__METHOD__, [
            'siteId' => $siteId,
            'siteUpdateStruct' => $siteUpdateStruct,
        ]);

        $this->siteHandler->update($siteId, $siteUpdateStruct);
        $invalidateTags = ['site-' . $siteId];
        /** @var \Ibexa\Contracts\SiteFactory\Values\Site\PublicAccess $publicAccess */
        foreach ($siteUpdateStruct->publicAccesses as $publicAccess) {
            $invalidateTags[] = 'ez-public-accesses-' . $this->escapeForCacheKey($publicAccess->getMatcherConfiguration()->host);
        }

        $this->cachePool->invalidateTags($invalidateTags);
    }

    public function load(int $id): Site
    {
        $this->logger->logCall(__METHOD__, [
            'id' => $id,
        ]);

        return $this->siteHandler->load($id);
    }

    public function delete(int $id): void
    {
        $this->logger->logCall(__METHOD__, [
            'id' => $id,
        ]);

        $this->siteHandler->delete($id);

        $this->cachePool->invalidateTags(['site-' . $id]);
    }
}

class_alias(SiteHandler::class, 'EzSystems\EzPlatformSiteFactory\Persistence\Cache\SiteHandler');
