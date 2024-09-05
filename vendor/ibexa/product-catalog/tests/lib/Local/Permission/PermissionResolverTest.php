<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ProductCatalog\Local\Permission;

use Ibexa\Contracts\Core\Repository\PermissionResolver as APIPermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\Product\View;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\ProductCatalog\Local\Permission\ContextProvider\Product\Product;
use Ibexa\ProductCatalog\Local\Permission\ContextResolver;
use Ibexa\ProductCatalog\Local\Permission\PermissionResolver;
use Ibexa\ProductCatalog\Local\Repository\Values\Product as LocalProduct;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @covers \Ibexa\ProductCatalog\Local\Permission\PermissionResolver
 */
final class PermissionResolverTest extends TestCase
{
    public function testCanUserWithoutObjectAndHasAccessFalse(): void
    {
        $apiPermissionResolver = $this->createMock(APIPermissionResolver::class);
        $apiPermissionResolver
            ->expects(self::once())
            ->method('hasAccess')
            ->willReturn(false);

        $permissionResolver = new PermissionResolver(
            $apiPermissionResolver,
            new ContextResolver([]),
            false
        );

        self::assertFalse($permissionResolver->canUser(new View()));
    }

    public function testCanUserWithoutObjectAndTrueHasAccess(): void
    {
        $apiPermissionResolver = $this->createMock(APIPermissionResolver::class);
        $apiPermissionResolver
            ->expects(self::once())
            ->method('hasAccess')
            ->willReturn(true);

        $permissionResolver = new PermissionResolver(
            $apiPermissionResolver,
            new ContextResolver([]),
            false
        );

        self::assertTrue($permissionResolver->canUser(new View()));
    }

    public function testCanUserWithContext(): void
    {
        $apiPermissionResolver = $this->createMock(APIPermissionResolver::class);
        $apiPermissionResolver
            ->expects(self::never())
            ->method('hasAccess');

        $content = $this->createMock(Content::class);
        $apiPermissionResolver
            ->expects(self::once())
            ->method('canUser')
            ->with(
                'product',
                'view',
                $content,
                []
            )
            ->willReturn(
                true
            );

        $permissionResolver = new PermissionResolver(
            $apiPermissionResolver,
            new ContextResolver([new Product()]),
            false
        );

        $policy = new View($this->getProduct());

        self::assertTrue($permissionResolver->canUser($policy));
    }

    public function testCanUserWithoutContextAndDebugEnabled(): void
    {
        $apiPermissionResolver = $this->createMock(APIPermissionResolver::class);

        $permissionContextResolver = new ContextResolver([]);

        $permissionResolver = new PermissionResolver(
            $apiPermissionResolver,
            $permissionContextResolver,
            true,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Argument 'policy' is invalid: Unable to find ContextProvider that can accept " . View::class
        );

        $permissionResolver->canUser(new View($this->getProduct()));
    }

    public function testCanUserWithoutContextAndDebugDisabled(): void
    {
        $apiPermissionResolver = $this->createMock(APIPermissionResolver::class);

        $permissionContextResolver = new ContextResolver([]);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('error')
            ->with("Argument 'policy' is invalid: Unable to find ContextProvider that can accept " . View::class);

        $permissionResolver = new PermissionResolver(
            $apiPermissionResolver,
            $permissionContextResolver,
            false,
            $logger
        );

        self::assertTrue($permissionResolver->canUser(new View($this->getProduct())));
    }

    private function getProduct(): LocalProduct
    {
        return new LocalProduct(
            $this->createMock(ProductTypeInterface::class),
            $this->createMock(Content::class),
            'code'
        );
    }
}
