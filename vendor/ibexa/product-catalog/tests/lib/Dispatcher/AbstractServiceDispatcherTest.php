<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Dispatcher;

use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Ibexa\ProductCatalog\Dispatcher\AbstractServiceDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @template K
 * @template T of AbstractServiceDispatcher<K>
 */
abstract class AbstractServiceDispatcherTest extends TestCase
{
    /**
     * @dataProvider dataProviderForTestDelegate
     *
     * @param array<int,mixed> $args
     * @param mixed $expectedReturnValue
     */
    final public function testDelegate(string $method, array $args = [], $expectedReturnValue = null): void
    {
        $service = $this->createMock($this->getTargetServiceClass());
        $service->expects(self::once())->method($method)->with(...$args)->willReturn($expectedReturnValue);

        $configProvider = $this->createMock(ConfigProviderInterface::class);
        $configProvider->method('getEngineType')->willReturn('default');

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('default')->willReturn($service);

        $dispatcher = $this->createDispatcherUnderTest($configProvider, $container);

        self::assertSame(
            $expectedReturnValue,
            $dispatcher->$method(...$args)
        );
    }

    /**
     * @return T
     */
    abstract protected function createDispatcherUnderTest(
        ConfigProviderInterface $configProvider,
        ContainerInterface $container
    ): AbstractServiceDispatcher;

    /**
     * @return class-string of K
     */
    abstract protected function getTargetServiceClass(): string;

    /**
     * @return iterable<int,array{string,array<mixed>,mixed}>
     */
    abstract public function dataProviderForTestDelegate(): iterable;
}
