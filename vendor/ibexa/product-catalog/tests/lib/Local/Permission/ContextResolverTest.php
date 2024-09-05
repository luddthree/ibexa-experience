<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Test\ProductCatalog\Local\Permission;

use Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException;
use Ibexa\Contracts\ProductCatalog\Permission\ContextInterface;
use Ibexa\Contracts\ProductCatalog\Permission\ContextProvider\ContextProviderInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\PolicyInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\ProductCatalog\Local\Permission\ContextResolver;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\ContextResolver
 */
final class ContextResolverTest extends TestCase
{
    private View $viewPolicy;

    public function setUp(): void
    {
        $this->viewPolicy = new View();
    }

    public function testResolve(): void
    {
        $contextA = $this->createMock(ContextInterface::class);
        $contextProviderA = $this->getContextProvider(
            $this->viewPolicy,
            true,
            $contextA
        );

        $contextProviderB = $this->getContextProvider(
            $this->viewPolicy,
            false,
            $this->createMock(ContextInterface::class)
        );

        $contextProviders = [
            $contextProviderA,
            $contextProviderB,
        ];

        $contextResolver = new ContextResolver($contextProviders);

        self::assertSame($contextA, $contextResolver->resolve($this->viewPolicy));
    }

    public function testResolveWillThrowExceptionWithoutContextProviders(): void
    {
        $contextResolver = new ContextResolver([]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectException(InvalidArgumentException::class);

        $contextResolver->resolve($this->viewPolicy);
    }

    public function testResolveWillThrowException(): void
    {
        $contextResolver = new ContextResolver(
            [
                $this->getContextProvider(
                    $this->viewPolicy,
                    false,
                    $this->createMock(ContextInterface::class)
                ),
            ]
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectException(InvalidArgumentException::class);

        $contextResolver->resolve($this->viewPolicy);
    }

    private function getContextProvider(
        PolicyInterface $policy,
        bool $accept,
        ContextInterface $returnedContext
    ): ContextProviderInterface {
        $contextProvider = $this->createMock(ContextProviderInterface::class);
        $contextProvider
            ->method('accept')
            ->with($policy)
            ->willReturn($accept);
        $contextProvider
            ->method('getPermissionContext')
            ->with($policy)
            ->willReturn($returnedContext);

        return $contextProvider;
    }
}
