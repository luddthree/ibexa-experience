<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\SiteFactory\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    private EventDispatcherInterface $innerEventDispatcher;

    private array $classMap;

    public function __construct(EventDispatcherInterface $innerEventDispatcher)
    {
        $this->innerEventDispatcher = $innerEventDispatcher;
        $this->classMap = require __DIR__ . '/../../bundle/Resources/mappings/class-map.php';
    }

    public function dispatch(object $event, string $eventName = null): object
    {
        return $this->innerEventDispatcher->dispatch($event, $eventName);
    }

    public function addListener(string $eventName, $listener, int $priority = 0): void
    {
        $newEventName = $this->resolveEventName($eventName);
        if (null !== $newEventName) {
            $this->innerEventDispatcher->addListener($newEventName, $listener, $priority);
        }
        $this->innerEventDispatcher->addListener($eventName, $listener, $priority);
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->innerEventDispatcher->addSubscriber($subscriber);
    }

    public function removeListener(string $eventName, callable $listener): void
    {
        $newEventName = $this->resolveEventName($eventName);

        if (null !== $newEventName) {
            $this->innerEventDispatcher->removeListener($newEventName, $listener);
        }

        $this->innerEventDispatcher->removeListener($eventName, $listener);
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        $this->innerEventDispatcher->removeSubscriber($subscriber);
    }

    public function getListeners(string $eventName = null): array
    {
        return $this->innerEventDispatcher->getListeners($eventName);
    }

    public function getListenerPriority(string $eventName, $listener): ?int
    {
        return $this->innerEventDispatcher->getListenerPriority($eventName, $listener);
    }

    public function hasListeners(string $eventName = null): bool
    {
        return $this->innerEventDispatcher->hasListeners($eventName);
    }

    private function resolveEventName(string $fullyQualifiedName): ?string
    {
        return $this->classMap[$fullyQualifiedName] ?? null;
    }
}
