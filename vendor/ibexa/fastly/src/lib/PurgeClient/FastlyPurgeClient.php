<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Fastly\PurgeClient;

use FOS\HttpCacheBundle\CacheManager;
use Ibexa\Contracts\HttpCache\PurgeClient\PurgeClientInterface;

/**
 * Purge client based on FOSHttpCacheBundle.
 *
 * Only support PURGE requests on purpose, to be able to invalidate cache for a
 * collection of Location/Content objects.
 */
class FastlyPurgeClient implements PurgeClientInterface
{
    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    private $cacheManager;

    public function __construct(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    public function purge(array $tags): void
    {
        $this->cacheManager->invalidateTags($tags);
    }

    public function purgeAll(): void
    {
        $this->cacheManager->invalidateTags(['ez-all']);
    }
}

class_alias(FastlyPurgeClient::class, 'EzSystems\PlatformFastlyCacheBundle\PurgeClient\FastlyPurgeClient');
