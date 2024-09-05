<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Dashboard\EventSubscriber;

use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Contracts\EventDispatcher\Event;

abstract class BaseEventSubscriberTestCase extends TestCase
{
    private TraceableEventDispatcher $eventDispatcher;

    /**
     * @return iterable<\Symfony\Component\EventDispatcher\EventSubscriberInterface>
     */
    abstract protected function buildEventSubscribers(): iterable;

    final protected function setUp(): void
    {
        $this->eventDispatcher = new TraceableEventDispatcher(new EventDispatcher(), new Stopwatch());
        foreach ($this->buildEventSubscribers() as $eventSubscriber) {
            $this->eventDispatcher->addSubscriber($eventSubscriber);
        }
    }

    abstract public function testGetSubscribedEvents(): void;

    /**
     * @param array<array{event: string, priority: int, method: string}>|null $expectedSubscriberCalls
     */
    protected function dispatch(Event $event, string $eventName, ?array $expectedSubscriberCalls): object
    {
        $result = $this->eventDispatcher->dispatch($event, $eventName);

        if (null !== $expectedSubscriberCalls) {
            self::assertSubscriberCalls($this->eventDispatcher->getCalledListeners(), $expectedSubscriberCalls);
        }

        return $result;
    }

    /**
     * @param array<array{event: string, priority: int, pretty: string}> $calledListener
     * @param array<array{event: string, priority: int, method: string}> $expectedSubscriberCalls
     */
    private static function assertSubscriberCalls(array $calledListener, array $expectedSubscriberCalls): void
    {
        // pre-process actual called listeners (subscribers) array for more verbose assert diff comparison
        $actualCalledListeners = array_map(
            static function (array $listenerData): array {
                return [
                    'event' => $listenerData['event'],
                    'priority' => $listenerData['priority'],
                    'method' => $listenerData['pretty'],
                ];
            },
            $calledListener
        );
        self::assertSame($expectedSubscriberCalls, $actualCalledListeners);
    }
}
