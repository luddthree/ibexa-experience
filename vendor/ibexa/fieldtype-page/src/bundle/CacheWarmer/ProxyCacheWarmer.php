<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\CacheWarmer;

use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\FieldTypePage\FieldType\LandingPage\Value;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final class ProxyCacheWarmer implements CacheWarmerInterface
{
    /** @var \Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface */
    private $proxyGenerator;

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
        $this->proxyGenerator->warmUp([
            Value::class,
        ]);

        return [];
    }
}
