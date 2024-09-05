<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\CacheWarmer;

use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\ProductCatalog\Local\Repository\Values\CustomerGroup;
use Ibexa\ProductCatalog\Local\Repository\Values\Product;
use Ibexa\ProductCatalog\Local\Repository\Values\ProductType;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final class ProxyCacheWarmer implements CacheWarmerInterface
{
    private const PROXY_CLASSES = [
        Product::class,
        ProductType::class,
        CustomerGroup::class,
    ];

    private ProxyGeneratorInterface $proxyGenerator;

    public function __construct(ProxyGeneratorInterface $proxyGenerator)
    {
        $this->proxyGenerator = $proxyGenerator;
    }

    public function isOptional(): bool
    {
        return false;
    }

    /**
     * @param string $cacheDir
     */
    public function warmUp($cacheDir): array
    {
        $this->proxyGenerator->warmUp(self::PROXY_CLASSES);

        return [];
    }
}
