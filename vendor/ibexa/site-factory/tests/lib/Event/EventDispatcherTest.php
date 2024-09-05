<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\SiteFactory\Event;

use Ibexa\Contracts\SiteFactory\Events\BeforeDeleteSiteEvent as ApiBeforeDeleteSiteEvent;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\SiteFactory\Event\EventDispatcher;
use Ibexa\SiteFactory\ServiceEvent\Events\BeforeDeleteSiteEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherTest extends TestCase
{
    public function testDispatch(): void
    {
        $site = new Site();
        $event = new BeforeDeleteSiteEvent($site);

        $inner = $this->createMock(EventDispatcherInterface::class);
        $inner
            ->expects(self::once())
            ->method('dispatch')
            ->with($event);
        $eventDispatcher = new EventDispatcher($inner);

        $eventDispatcher->dispatch($event);
    }

    public function testAddListener(): void
    {
        $expected = [ApiBeforeDeleteSiteEvent::class, BeforeDeleteSiteEvent::class];
        $matcher = self::exactly(count($expected));

        $inner = $this->createMock(EventDispatcherInterface::class);
        $inner
            ->expects($matcher)
            ->method('addListener')
            ->with(
                $this->callback(function ($eventName) use ($expected, $matcher) {
                    $this->assertEquals($eventName, $expected[$matcher->getInvocationCount() - 1]);

                    return true;
                })
            );

        $eventDispatcher = new EventDispatcher($inner);

        $eventDispatcher->addListener(
            BeforeDeleteSiteEvent::class,
            static function () {},
            10
        );
    }

    public function testRemoveListener(): void
    {
        $expected = [ApiBeforeDeleteSiteEvent::class, BeforeDeleteSiteEvent::class];
        $matcher = self::exactly(count($expected));

        $inner = $this->createMock(EventDispatcherInterface::class);
        $inner
            ->expects($matcher)
            ->method('removeListener')
            ->with(
                $this->callback(function ($eventName) use ($expected, $matcher) {
                    $this->assertEquals($eventName, $expected[$matcher->getInvocationCount() - 1]);

                    return true;
                })
            );

        $eventDispatcher = new EventDispatcher($inner);

        $eventDispatcher->removeListener(
            BeforeDeleteSiteEvent::class,
            static function () {},
        );
    }
}
