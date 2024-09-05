<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\ProductCatalog\CacheWarmer;

use Ibexa\Bundle\ProductCatalog\CacheWarmer\ProxyCacheWarmer;
use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use PHPUnit\Framework\TestCase;

final class ProxyCacheWarmerTest extends TestCase
{
    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ProxyGeneratorInterface $proxyGenerator;

    private ProxyCacheWarmer $proxyCacheWarmer;

    protected function setUp(): void
    {
        $this->proxyGenerator = $this->createMock(ProxyGeneratorInterface::class);
        $this->proxyCacheWarmer = new ProxyCacheWarmer($this->proxyGenerator);
    }

    public function testIsOptional(): void
    {
        self::assertFalse($this->proxyCacheWarmer->isOptional());
    }

    public function testWarmUp(): void
    {
        $expectedClasses = [
            Product::class,
            ProductType::class,
            CustomerGroup::class,
        ];

        $this->proxyGenerator
            ->expects(self::once())
            ->method('warmUp')
            ->with($expectedClasses);

        $this->proxyCacheWarmer->warmUp('/cache/dir');
    }
}
