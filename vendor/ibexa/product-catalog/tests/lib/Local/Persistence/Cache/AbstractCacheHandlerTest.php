<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Persistence\Cache;

use Closure;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface;
use Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierSanitizer;
use Ibexa\Core\Persistence\Cache\PersistenceLogger;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\CacheItem;

/**
 * Abstract test case for SPI cache.
 */
abstract class AbstractCacheHandlerTest extends TestCase
{
    /** @var \Ibexa\Core\Persistence\Cache\PersistenceLogger|\PHPUnit\Framework\MockObject\MockObject */
    protected PersistenceLogger $loggerMock;

    /** @var \Ibexa\Core\Persistence\Cache\Identifier\CacheIdentifierGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected CacheIdentifierGeneratorInterface $cacheIdentifierGeneratorMock;

    protected CacheIdentifierSanitizer $cacheIdentifierSanitizer;

    /** @var \Symfony\Component\Cache\Adapter\TagAwareAdapter|\PHPUnit\Framework\MockObject\MockObject */
    protected TagAwareAdapter $cacheMock;

    private Closure $cacheItemsClosure;

    abstract public function getPersistenceCacheHandler(object $innerHandler): object;

    /**
     * @return object|\PHPUnit\Framework\MockObject\MockObject
     */
    abstract public function getInnerHandler(): object;

    /**
     * @phpstan-return iterable<int,array{
     *     string,
     *     array<mixed>,
     *     2?: array<mixed>|null,
     *     3?: array<mixed>|null,
     *     4?: array<string>|null,
     *     5?: string|array<string>|null,
     *     6?: mixed,
     * }>
     */
    abstract public function providerForUnCachedMethods(): iterable;

    protected function setUp(): void
    {
        $this->loggerMock = $this->createMock(PersistenceLogger::class);
        $this->cacheIdentifierGeneratorMock = $this->createMock(CacheIdentifierGeneratorInterface::class);
        $this->cacheIdentifierSanitizer = new CacheIdentifierSanitizer();
        $this->cacheMock = $this->createMock(TagAwareAdapter::class);

        $this->cacheItemsClosure = Closure::bind(
            static function ($key, $value, $isHit) {
                $item = new CacheItem();
                $item->key = $key;
                $item->value = $value;
                $item->isHit = $isHit;
                $item->isTaggable = true;

                return $item;
            },
            null,
            CacheItem::class
        );
    }

    /**
     * @dataProvider providerForUnCachedMethods
     *
     * @param string $method
     * @param array<mixed> $arguments
     * @param array<mixed>|null $tagGeneratingArguments
     * @param array<mixed>|null $keyGeneratingArguments
     * @param array<string>|null $tags
     * @param string|array<string>|null $key
     * @param mixed $returnValue
     */
    final public function testUnCachedMethods(
        string $method,
        array $arguments,
        array $tagGeneratingArguments = null,
        array $keyGeneratingArguments = null,
        array $tags = null,
        $key = null,
        $returnValue = null
    ): void {
        $this->loggerMock->expects(self::once())->method('logCall');
        $this->loggerMock->expects(self::never())->method('logCacheHit');
        $this->loggerMock->expects(self::never())->method('logCacheMiss');

        $innerHandler = $this->getInnerHandler();
        $invocationMocker = $innerHandler
            ->expects(self::once())
            ->method($method)
            ->with(...$arguments);

        // workaround for mocking void-returning methods, null in this case denotes that, not null value
        if (null !== $returnValue) {
            $invocationMocker->willReturn($returnValue);
        }

        if ($tags || $key) {
            if ($tagGeneratingArguments && $tags !== null) {
                $this->cacheIdentifierGeneratorMock
                    ->expects(self::exactly(count($tagGeneratingArguments)))
                    ->method('generateTag')
                    ->withConsecutive(...$tagGeneratingArguments)
                    ->willReturnOnConsecutiveCalls(...$tags);
            }

            if ($keyGeneratingArguments) {
                $callsCount = count($keyGeneratingArguments);

                if (is_array($key)) {
                    $this->cacheIdentifierGeneratorMock
                        ->expects(self::exactly($callsCount))
                        ->method('generateKey')
                        ->withConsecutive(...$keyGeneratingArguments)
                        ->willReturnOnConsecutiveCalls(...$key);
                } else {
                    $this->cacheIdentifierGeneratorMock
                        ->expects(self::exactly($callsCount))
                        ->method('generateKey')
                        ->with($keyGeneratingArguments[0][0])
                        ->willReturn($key);
                }
            }
            $this->cacheMock
                ->expects(!empty($tags) ? self::once() : self::never())
                ->method('invalidateTags')
                ->with($tags);

            $this->cacheMock
                ->expects(!empty($key) && is_string($key) ? self::once() : self::never())
                ->method('deleteItem')
                ->with($key);

            $this->cacheMock
                ->expects(!empty($key) && is_array($key) ? self::once() : self::never())
                ->method('deleteItems')
                ->with($key);
        }

        self::assertEquals(
            $returnValue,
            $this->getActualReturnValue(
                $this->getPersistenceCacheHandler($innerHandler),
                $method,
                $arguments
            )
        );
    }

    /**
     * @phpstan-return iterable<int,array{
     *     string,
     *     array<mixed>,
     *     string,
     *     3?: array<mixed>|null,
     *     4?: array<string>|null,
     *     5?: array<mixed>|null,
     *     6?: array<string>|null,
     *     7?: mixed,
     *     8?: bool,
     * }>
     */
    abstract public function providerForCachedLoadMethodsHit(): iterable;

    /**
     * @dataProvider providerForCachedLoadMethodsHit
     *
     * @param string $method
     * @param array<mixed> $arguments
     * @param string $key
     * @param array<mixed>|null $tagGeneratingArguments
     * @param array<string>|null $tagGeneratingResults
     * @param array<mixed>|null $keyGeneratingArguments
     * @param array<string>|null $keyGeneratingResults
     * @param mixed $data
     * @param bool $multi Default false, set to true if method will lookup several cache items.
     */
    final public function testLoadMethodsCacheHit(
        string $method,
        array $arguments,
        string $key,
        array $tagGeneratingArguments = null,
        array $tagGeneratingResults = null,
        array $keyGeneratingArguments = null,
        array $keyGeneratingResults = null,
        $data = null,
        bool $multi = false
    ): void {
        $cacheItem = $this->getCacheItem($key, $multi ? reset($data) : $data);

        $this->loggerMock->expects(self::never())->method('logCall');

        if ($tagGeneratingArguments && $tagGeneratingResults !== null) {
            $this->cacheIdentifierGeneratorMock
                ->expects(self::exactly(count($tagGeneratingArguments)))
                ->method('generateTag')
                ->withConsecutive(...$tagGeneratingArguments)
                ->willReturnOnConsecutiveCalls(...$tagGeneratingResults);
        }

        if ($keyGeneratingArguments && $keyGeneratingResults !== null) {
            $this->cacheIdentifierGeneratorMock
                ->expects(self::exactly(count($keyGeneratingArguments)))
                ->method('generateKey')
                ->withConsecutive(...$keyGeneratingArguments)
                ->willReturnOnConsecutiveCalls(...$keyGeneratingResults);
        }

        if ($multi) {
            $this->cacheMock
                ->expects(self::once())
                ->method('getItems')
                ->with([$cacheItem->getKey()])
                ->willReturn([$key => $cacheItem]);
        } else {
            $this->cacheMock
                ->expects(self::once())
                ->method('getItem')
                ->with($cacheItem->getKey())
                ->willReturn($cacheItem);
        }

        self::assertEquals(
            $data,
            $this->getActualReturnValue(
                $this->getPersistenceCacheHandler($this->getInnerHandler()),
                $method,
                $arguments
            )
        );
    }

    /**
     * @phpstan-return iterable<int,array{
     *     string,
     *     array<mixed>,
     *     string,
     *     3?: array<mixed>|null,
     *     4?: array<string>|null,
     *     5?: array<mixed>|null,
     *     6?: array<string>|null,
     *     7?: mixed|null,
     *     8?: bool,
     * }>
     */
    abstract public function providerForCachedLoadMethodsMiss(): iterable;

    /**
     * @dataProvider providerForCachedLoadMethodsMiss
     *
     * @param string $method
     * @param array<mixed> $arguments
     * @param string $key
     * @param array<mixed>|null $tagGeneratingArguments
     * @param array<string>|null $tagGeneratingResults
     * @param array<mixed>|null $keyGeneratingArguments
     * @param array<string>|null $keyGeneratingResults
     * @param mixed|null $data
     * @param bool $multi Default false, set to true if method will lookup several cache items.
     */
    final public function testLoadMethodsCacheMiss(
        string $method,
        array $arguments,
        string $key,
        array $tagGeneratingArguments = null,
        array $tagGeneratingResults = null,
        array $keyGeneratingArguments = null,
        array $keyGeneratingResults = null,
        $data = null,
        bool $multi = false
    ): void {
        $cacheItem = $this->getCacheItem($key);

        $this->loggerMock->expects(self::once())->method('logCall');

        if ($tagGeneratingArguments && $tagGeneratingResults !== null) {
            $this->cacheIdentifierGeneratorMock
                ->expects(self::exactly(count($tagGeneratingArguments)))
                ->method('generateTag')
                ->withConsecutive(...$tagGeneratingArguments)
                ->willReturnOnConsecutiveCalls(...$tagGeneratingResults);
        }

        if ($keyGeneratingArguments && $keyGeneratingResults !== null) {
            $this->cacheIdentifierGeneratorMock
                ->expects(self::exactly(count($keyGeneratingArguments)))
                ->method('generateKey')
                ->withConsecutive(...$keyGeneratingArguments)
                ->willReturnOnConsecutiveCalls(...$keyGeneratingResults);
        }

        if ($multi) {
            $this->cacheMock
                ->expects(self::once())
                ->method('getItems')
                ->with([$cacheItem->getKey()])
                ->willReturn([$key => $cacheItem]);
        } else {
            $this->cacheMock
                ->expects(self::once())
                ->method('getItem')
                ->with($cacheItem->getKey())
                ->willReturn($cacheItem);
        }
        $innerHandler = $this->getInnerHandler();
        $innerHandler
            ->expects(self::once())
            ->method($method)
            ->with(...$arguments)
            ->willReturn($data);

        $this->cacheMock
            ->expects(self::once())
            ->method('save')
            ->with($cacheItem);

        self::assertEquals(
            $data,
            $this->getActualReturnValue(
                $this->getPersistenceCacheHandler($innerHandler),
                $method,
                $arguments
            )
        );
    }

    /**
     * @param mixed|null $value If null the cache item will be assumed to be a cache miss here.
     */
    final protected function getCacheItem(string $key, $value = null, int $defaultLifetime = 0): CacheItem
    {
        $cacheItemsClosure = $this->cacheItemsClosure;

        return $cacheItemsClosure($key, $value, (bool)$value, $defaultLifetime);
    }

    /**
     * @param array<mixed> $arguments
     *
     * @return false|mixed|void
     */
    private function getActualReturnValue(
        object $handler,
        string $method,
        array $arguments
    ) {
        $callable = [$handler, $method];

        if (is_callable($callable)) {
            return $callable(...$arguments);
        }

        self::fail(
            sprintf(
                'Method %s of class %s is not callable',
                $method,
                get_class($handler)
            )
        );
    }
}
