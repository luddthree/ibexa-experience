<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Dashboard\News;

use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Dashboard\News\CachedFeed;
use Ibexa\Dashboard\News\FeedInterface;
use Ibexa\Dashboard\News\Values\NewsItem;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * @covers \Ibexa\Dashboard\News\CachedFeed
 */
final class CachedFeedTest extends TestCase
{
    private const URL = 'https://foo/bar';
    private const HASHED_URL = '36dca1a8a050a88147fe51db23b2d12e';
    private const LIMIT = 5;
    private const TTL = 3600;

    private CachedFeed $cachedFeed;

    /** @var \Symfony\Contracts\Cache\ItemInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ItemInterface $cacheItemMock;

    /** @var \Ibexa\Dashboard\News\FeedInterface&\PHPUnit\Framework\MockObject\MockObject */
    private FeedInterface $feedMock;

    protected function setUp(): void
    {
        $this->feedMock = $this->createMock(FeedInterface::class);
        $cachePoolMock = $this->createMock(AdapterInterface::class);

        $cacheIdentifierGeneratorMock = $this->createMock(CacheIdentifierGeneratorInterface::class);
        $cacheIdentifierGeneratorMock
            ->method('generateKey')
            ->with('rss', [self::HASHED_URL, self::LIMIT])
            ->willReturn('key-mock')
        ;

        $this->cacheItemMock = $this->createMock(ItemInterface::class);

        $cachePoolMock->method('getItem')->with('key-mock')->willReturn($this->cacheItemMock);

        $this->cachedFeed = new CachedFeed(
            $this->feedMock,
            $cachePoolMock,
            $cacheIdentifierGeneratorMock,
            self::TTL
        );
    }

    /**
     * @throws \Ibexa\Dashboard\News\FeedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testFetchCached(): void
    {
        $this->cacheItemMock->method('isHit')->willReturn(true);
        $this->cacheItemMock->method('get')->willReturn($this->buildNewsList());
        $this->feedMock->expects(self::never())->method('fetch');

        $this->cachedFeed->fetch(self::URL, self::LIMIT);
    }

    /**
     * @throws \Ibexa\Dashboard\News\FeedException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function testFetchUncached(): void
    {
        $this->cacheItemMock->method('isHit')->willReturn(false);
        $newsList = $this->buildNewsList();
        $this
            ->feedMock
            ->expects(self::once())
            ->method('fetch')
            ->with(self::URL, self::LIMIT)
            ->willReturn($newsList)
        ;
        $this->cacheItemMock->method('set')->with($newsList);
        $this->cacheItemMock->method('expiresAfter')->with(self::TTL);

        $this->cachedFeed->fetch(self::URL, self::LIMIT);
    }

    /**
     * @return \Ibexa\Dashboard\News\Values\NewsItem[]
     */
    private function buildNewsList(): array
    {
        return [new NewsItem(), new NewsItem()];
    }
}
