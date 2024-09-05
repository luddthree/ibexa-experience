<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Proxy;

use Ibexa\Core\Repository\ProxyFactory\ProxyGeneratorInterface;
use Ibexa\Workflow\Value\WorkflowMarkingCollection;
use Ibexa\Workflow\Value\WorkflowTransitionCollection;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

final class ProxyCacheWarmer implements CacheWarmerInterface
{
    public const PROXY_CLASSES = [
        WorkflowMarkingCollection::class,
        WorkflowTransitionCollection::class,
    ];

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

    public function warmUp($cacheDir): void
    {
        $this->proxyGenerator->warmUp(self::PROXY_CLASSES);
    }
}

class_alias(ProxyCacheWarmer::class, 'EzSystems\EzPlatformWorkflow\Proxy\ProxyCacheWarmer');
