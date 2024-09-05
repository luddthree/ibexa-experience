<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Cache;

use Ibexa\Core\Persistence\Cache\AbstractHandler;
use Psr\Cache\CacheItemInterface;

abstract class AbstractCacheServiceDecorator extends AbstractHandler
{
    protected const DEFAULT_EXPIRATION_TIME = 300;
    protected const CACHE_KEY_PREFIX = 'ibexa';
    protected const CACHE_KEY_SEPARATOR = '-';

    /** @var \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface */
    protected $cache;

    protected function processCacheItem(
        string $key,
        array $tags,
        callable $callback,
        ?int $expirationTime = null
    ): CacheItemInterface {
        $item = $this->cache->getItem($key);

        if (false === $item->isHit()) {
            $item
                ->set($callback())
                ->tag($tags)
                ->expiresAfter($expirationTime);

            $this->cache->save($item);
        }

        return $item;
    }

    protected function removeCacheItem(
        string $key,
        array $arguments
    ): void {
        $this->cache->deleteItem(
            $this->buildCacheKey($arguments)
        );
        $this->cache->invalidateTags(
            $this->buildCacheTagKeys($key, $arguments)
        );
    }

    protected function buildCacheKey(array $arguments): string
    {
        return $this->cacheIdentifierSanitizer->escapeForCacheKey(
            implode(
                self::CACHE_KEY_SEPARATOR,
                array_merge(
                    [self::CACHE_KEY_PREFIX],
                    $arguments
                )
            )
        );
    }

    protected function buildCacheTagKeys(string $key, array $arguments): array
    {
        return [
            $this->cacheIdentifierSanitizer->escapeForCacheKey($key),
            $this->buildCacheTagKey($arguments),
        ];
    }

    protected function buildCacheTagKey(array $arguments)
    {
        return $this->cacheIdentifierSanitizer->escapeForCacheKey(
            implode(
                self::CACHE_KEY_SEPARATOR,
                $arguments
            )
        );
    }
}

class_alias(AbstractCacheServiceDecorator::class, 'Ibexa\Platform\Personalization\Cache\AbstractCacheServiceDecorator');
