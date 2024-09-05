<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\CorporateAccount\Event;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Stopwatch\Stopwatch;

abstract class AbstractServiceTest extends TestCase
{
    protected TraceableEventDispatcher $eventDispatcher;

    /** @return class-string */
    abstract protected function getEventServiceClass(): string;

    /** @return class-string */
    abstract protected function getServiceInterface(): string;

    /**
     * @return array<string, array{
     *     string,
     *     array<class-string, callable>,
     *     array<int, mixed>,
     *     3?: mixed
     * }>
     */
    abstract public function getTestServiceMethods(): array;

    /**
     * @return array<string, array{
     *     string,
     *     array<class-string, callable>,
     *     array<int, mixed>,
     *     3?: mixed,
     *     4?: mixed
     * }>
     */
    abstract public function getInterruptedTestServiceMethods(): array;

    protected function setUp(): void
    {
        $this->eventDispatcher = new TraceableEventDispatcher(
            new EventDispatcher(),
            new Stopwatch()
        );
    }

    public function testMethodCoverage(): void
    {
        $eventServiceMethods = $this->getPublicMethodNames(
            $this->getEventServiceClass()
        );

        $interfaceServiceMethods = $this->getPublicMethodNames(
            $this->getServiceInterface()
        );

        $expectedMethodCoverage = array_intersect(
            $eventServiceMethods,
            $interfaceServiceMethods
        );

        self::assertEqualsCanonicalizing(
            $expectedMethodCoverage,
            array_column($this->getTestServiceMethods(), 0),
            'test method coverage'
        );

        self::assertEqualsCanonicalizing(
            $expectedMethodCoverage,
            array_column($this->getInterruptedTestServiceMethods(), 0),
            'interrupted test method coverage'
        );
    }

    /**
     * @dataProvider getTestServiceMethods
     *
     * @param array<class-string, callable> $listeners
     * @param array<int, mixed> $arguments
     * @param mixed|null $return
     */
    public function testBeforeAndAfterEventsForServiceMethod(
        string $method,
        array $listeners,
        array $arguments = [],
        $return = null
    ): void {
        $eventServiceClass = $this->getEventServiceClass();
        $innerService = self::createMock($this->getServiceInterface());

        foreach ($listeners as $eventName => $listener) {
            $this->eventDispatcher->addListener($eventName, $listener);
        }

        $service = new $eventServiceClass(
            $innerService,
            $this->eventDispatcher
        );

        $methodInvocation = $innerService
            ->expects(self::once())
            ->method($method);

        if ($return !== null) {
            $methodInvocation->willReturn($return);
        }

        $result = $service->$method(...$arguments);

        self::assertSame($return, $result);
        self::assertEquals(
            [],
            $this->eventDispatcher->getOrphanedEvents(),
        );
        self::assertEquals(
            array_keys($listeners),
            array_column($this->eventDispatcher->getCalledListeners(), 'event')
        );
    }

    /**
     * @dataProvider getInterruptedTestServiceMethods
     *
     * @param array<class-string, callable> $listeners
     * @param array<int, mixed> $arguments
     * @param mixed|null $return
     * @param mixed|null $interruptedReturn
     */
    public function testInterruptedBeforeAndAfterEventsForServiceMethod(
        string $method,
        array $listeners,
        array $arguments = [],
        $return = null,
        $interruptedReturn = null
    ): void {
        $eventServiceClass = $this->getEventServiceClass();
        $innerService = self::createMock($this->getServiceInterface());

        foreach ($listeners as $eventName => $listener) {
            $this->eventDispatcher->addListener($eventName, $listener);
        }

        $service = new $eventServiceClass(
            $innerService,
            $this->eventDispatcher
        );

        $innerService
            ->expects(self::never())
            ->method($method);

        if ($return !== null) {
            $result = $service->$method(...$arguments);

            self::assertSame($interruptedReturn, $result);
            self::assertNotSame($interruptedReturn, $return);
        } else {
            $service->$method(...$arguments);
        }
    }

    /**
     * @param class-string $className
     *
     * @return string[]
     */
    protected function getPublicMethodNames(
        string $className
    ): array {
        $class = new ReflectionClass($className);
        $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

        return array_filter(
            array_map(
                static function (ReflectionMethod $method) use ($className): ?string {
                    if ($method->class === $className) {
                        return $method->name;
                    }

                    return null;
                },
                $methods
            )
        );
    }
}
