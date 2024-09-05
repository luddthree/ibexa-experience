<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Fastly\DependencyInjection\Compiler;

use Ibexa\Fastly\ProxyClient\Fastly;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KernelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $purgeType = $container->getParameter('ibexa.http_cache.purge_type');
        if ($purgeType === 'fastly') {
            // Injecting our own Fastly ProxyClient instead of FOS'
            $container->setParameter('ezplatform.http_cache.tags.header', Fastly::TAG_HEADER_NAME);
            // Due to loading order, we need to make sure that this parameter is also set correctly
            $container->setParameter('fos_http_cache.tag_handler.response_header', Fastly::TAG_HEADER_NAME);
            $container->setParameter('fos_http_cache.tag_handler.separator', ' ');

            $container->setAlias(
                'fos_http_cache.default_proxy_client',
                Fastly::class
            );
        }
    }
}

class_alias(KernelPass::class, 'EzSystems\PlatformFastlyCacheBundle\DependencyInjection\Compiler\KernelPass');
