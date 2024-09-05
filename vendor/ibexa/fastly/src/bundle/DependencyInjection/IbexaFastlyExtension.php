<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Fastly\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IbexaFastlyExtension extends Extension
{
    public const EXTENSION_NAME = 'ibexa_fastly';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $this->processHttpDispatcherParameters($container);
    }

    private function processHttpDispatcherParameters(ContainerBuilder $container): void
    {
        $container->setParameter(
            'ibexa.http_cache.fastly.http.servers',
            ['https://api.fastly.com']
        );

        /** @todo add dynamic setting for base_url in httpCacheBundle */
        $container->setParameter(
            'ibexa.http_cache.fastly.http.base_url',
            ''
        );
    }
}

class_alias(IbexaFastlyExtension::class, 'EzSystems\PlatformFastlyCacheBundle\DependencyInjection\EzPlatformFastlyCacheExtension');
