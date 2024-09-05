<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Fastly;

use Ibexa\Bundle\Fastly\DependencyInjection\Compiler\KernelPass;
use Ibexa\Bundle\Fastly\DependencyInjection\Configuration\Parser\FastlyConfigParser;
use Ibexa\Bundle\Fastly\DependencyInjection\Configuration\Parser\FastlyVariationsParser;
use Ibexa\Bundle\Fastly\DependencyInjection\IbexaFastlyExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaFastlyBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $core->addConfigParser(new FastlyVariationsParser());

        /** @var \Ibexa\Bundle\HttpCache\DependencyInjection\IbexaHttpCacheExtension $httpCacheExtension */
        $httpCacheExtension = $container->getExtension('ibexa_http_cache');
        $httpCacheExtension->addExtraConfigParser(new FastlyConfigParser());

        $container->addCompilerPass(new KernelPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new IbexaFastlyExtension();
        }

        return $this->extension;
    }
}

class_alias(IbexaFastlyBundle::class, 'EzSystems\PlatformFastlyCacheBundle\EzSystemsPlatformFastlyCacheBundle');
