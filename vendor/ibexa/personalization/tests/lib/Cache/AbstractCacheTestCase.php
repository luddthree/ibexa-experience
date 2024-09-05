<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Cache;

use Ibexa\Contracts\Core\Persistence\Handler as PersistenceHandler;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\LocationPathConverter;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use Symfony\Component\Cache\CacheItem;

abstract class AbstractCacheTestCase extends TestCase
{
    /** @var \Symfony\Component\Cache\Adapter\TagAwareAdapterInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $cache;

    /** @var \Ibexa\Contracts\Core\Persistence\Handler|\PHPUnit\Framework\MockObject\MockObject */
    protected $persistenceHandler;

    /** @var \Ibexa\Core\Persistence\Cache\PersistenceLogger|\PHPUnit\Framework\MockObject\MockObject */
    protected $persistenceLogger;

    /** @var \Closure */
    private $cacheItemsClosure;

    /** @var \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $cacheIdentifierGenerator;

    /** @var \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer */
    protected $cacheIdentifierSanitizer;

    /** @var \Ibexa\Core\Persistence\Cache\LocationPathConverter */
    protected $locationPathConverter;

    protected function setUp(): void
    {
        $this->cache = $this->createMock(TagAwareAdapterInterface::class);
        $this->persistenceHandler = $this->createMock(PersistenceHandler::class);
        $this->persistenceLogger = $this->createMock(PersistenceLogger::class);
        $this->cacheIdentifierGenerator = $this->createMock(
            CacheIdentifierGeneratorInterface::class
        );
        $this->cacheIdentifierSanitizer = new CacheIdentifierSanitizer();
        $this->locationPathConverter = new LocationPathConverter();
        $this->cacheItemsClosure = \Closure::bind(
            static function ($key, $value, $isHit, $defaultLifetime = 0) {
                $item = new CacheItem();
                $item->key = $key;
                $item->value = $value;
                $item->isHit = $isHit;
                $item->defaultLifetime = $defaultLifetime;
                $item->isTaggable = true;

                return $item;
            },
            null,
            CacheItem::class
        );
    }

    final protected function getCacheItem($key, $value = null, $defaultLifetime = 0): CacheItemInterface
    {
        $cacheItemsClosure = $this->cacheItemsClosure;

        return $cacheItemsClosure($key, $value, (bool)$value, $defaultLifetime);
    }
}

class_alias(AbstractCacheTestCase::class, 'Ibexa\Platform\Tests\Personalization\Cache\AbstractCacheTestCase');
