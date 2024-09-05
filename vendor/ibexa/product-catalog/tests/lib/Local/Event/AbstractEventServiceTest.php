<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Event;

use Ibexa\Contracts\Core\Repository\Event\BeforeEvent;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @template T
 */
abstract class AbstractEventServiceTest extends TestCase
{
    /** @var \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected MockObject $eventDispatcher;

    /** @var T|\PHPUnit\Framework\MockObject\MockObject */
    protected MockObject $innerService;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->innerService = $this->createMock($this->getInnerServiceClass());
    }

    /**
     * @return class-string<T>
     */
    abstract protected function getInnerServiceClass(): string;

    protected function assertBeforeAndAfterEventsAreDispatched(
        Constraint $beforeEventConstraint,
        Constraint $afterEventConstraint
    ): void {
        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$beforeEventConstraint],
                [$afterEventConstraint]
            );
    }

    /**
     * @param callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void $resultCallback
     */
    protected function assertBeforeEventIsDispatchedAndStopsPropagation(
        Constraint $constraint,
        ?callable $resultCallback = null
    ): void {
        $callback = static function (BeforeEvent $event) use ($resultCallback): Event {
            if ($resultCallback !== null) {
                $resultCallback($event);
            }

            $event->stopPropagation();

            return $event;
        };

        $this->eventDispatcher
            ->expects(self::once())
            ->method('dispatch')
            ->with($constraint)
            ->willReturnCallback($callback);
    }

    /**
     * @param callable(\Ibexa\Contracts\Core\Repository\Event\BeforeEvent): void $resultCallback
     */
    protected function assertBeforeAndAfterEventsAreDispatchedWithOverwrittenResult(
        Constraint $beforeEventConstraint,
        Constraint $afterEventConstraint,
        callable $resultCallback
    ): void {
        $callback = static function (Event $event) use ($resultCallback): Event {
            if ($event instanceof BeforeEvent) {
                $resultCallback($event);
            }

            return $event;
        };

        $this->eventDispatcher
            ->expects(self::exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$beforeEventConstraint],
                [$afterEventConstraint]
            )
            ->willReturnCallback($callback);
    }

    /**
     * @template E of \Symfony\Contracts\EventDispatcher\Event
     *
     * @param class-string<E> $class
     * @param callable(E): bool $callback
     */
    protected function isValidEvent(string $class, callable $callback): Constraint
    {
        return self::logicalAnd(self::isInstanceOf($class), self::callback($callback));
    }

    /**
     * @param array<mixed> $args
     */
    protected function assertInnerServiceIsNotCalled(string $method, array $args = []): void
    {
        $this->innerService
            ->expects(self::never())
            ->method($method)
            ->with(...$args);
    }

    /**
     * @param array<int,mixed> $args
     * @param mixed $result
     */
    protected function assertInnerServiceIsCalled(string $method, array $args = [], $result = null): void
    {
        $invocation = $this->innerService->expects(self::once())->method($method)->with(...$args);

        if ($result !== null) {
            $invocation->willReturn($result);
        }
    }
}
