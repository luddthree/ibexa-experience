<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteContext;

use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\SiteContext\Event\ResolveLocationPreviewUrlEvent;
use Ibexa\Contracts\SiteContext\Exception\UnresolvedPreviewUrlException;
use Ibexa\SiteContext\PreviewUrlResolver\LocationPreviewUrlResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class LocationPreviewUrlResolverTest extends TestCase
{
    private const EXAMPLE_URL = 'https://www.example.com';
    private const EXAMPLE_CONTEXT = [
        'foo' => 'bar',
        'baz' => 'qux',
    ];

    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface&\PHPUnit\Framework\MockObject\MockObject */
    private EventDispatcherInterface $eventDispatcher;

    private LocationPreviewUrlResolver $resolver;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->resolver = new LocationPreviewUrlResolver($this->eventDispatcher);
    }

    public function testResolveUrl(): void
    {
        $location = $this->createMock(Location::class);

        $this->eventDispatcher
            ->method('dispatch')
            ->with($this->callback(static function ($event) use ($location): bool {
                return $event instanceof ResolveLocationPreviewUrlEvent
                    && $event->getLocation() === $location
                    && $event->getContext() === self::EXAMPLE_CONTEXT;
            }))
            ->willReturnCallback(static function (ResolveLocationPreviewUrlEvent $event): ResolveLocationPreviewUrlEvent {
                $event->setPreviewUrl(self::EXAMPLE_URL);

                return $event;
            });

        self::assertEquals(
            self::EXAMPLE_URL,
            $this->resolver->resolveUrl($location, self::EXAMPLE_CONTEXT)
        );
    }

    public function testResolveUrlThrowsExceptionWhenPreviewUrlIsNotSet(): void
    {
        $this->expectException(UnresolvedPreviewUrlException::class);
        $this->expectExceptionMessage('Preview URL for location "42" could not be resolved.');

        $location = $this->createMock(Location::class);
        $location->method('__get')->with('id')->willReturn(42);

        $this->eventDispatcher
            ->method('dispatch')
            ->willReturnCallback(static fn (ResolveLocationPreviewUrlEvent $event): ResolveLocationPreviewUrlEvent => $event);

        $this->resolver->resolveUrl($location);
    }
}
