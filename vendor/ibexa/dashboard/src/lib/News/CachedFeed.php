<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Dashboard\News;

use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @internal
 */
final class CachedFeed implements FeedInterface
{
    private const RSS_CACHE_KEY_PATTERN = 'rss';

    private FeedInterface $feed;

    private AdapterInterface $cachePool;

    private CacheIdentifierGeneratorInterface $cacheIdentifierGenerator;

    private int $ttl;

    public function __construct(
        FeedInterface $feed,
        AdapterInterface $cachePool,
        CacheIdentifierGeneratorInterface $cacheIdentifierGenerator,
        int $ttl
    ) {
        $this->feed = $feed;
        $this->cachePool = $cachePool;
        $this->cacheIdentifierGenerator = $cacheIdentifierGenerator;
        $this->ttl = $ttl;
    }

    /**
     * @throws \Ibexa\Dashboard\News\FeedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     */
    public function fetch(string $url, int $limit): array
    {
        $cacheItem = $this->cachePool->getItem(
            $this->cacheIdentifierGenerator->generateKey(
                self::RSS_CACHE_KEY_PATTERN,
                [
                    md5($url),
                    $limit,
                ]
            )
        );

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $news = $this->feed->fetch($url, $limit);

        $cacheItem->set($news);
        $cacheItem->expiresAfter($this->ttl);

        $this->cachePool->save($cacheItem);

        return $news;
    }
}
