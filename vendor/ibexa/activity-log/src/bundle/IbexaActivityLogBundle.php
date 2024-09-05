<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog;

use Ibexa\ActivityLog\Permission\PolicyProvider;
use Ibexa\Bundle\ActivityLog\DependencyInjection\CompilerPass\PersistenceSerializerCompilerPass;
use Ibexa\Bundle\ActivityLog\DependencyInjection\Configuration\SiteAccessAware\ActivityLogParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaActivityLogBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new PersistenceSerializerCompilerPass());

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernel */
        $kernel = $container->getExtension('ibexa');
        $kernel->addConfigParser(new ActivityLogParser());
        $kernel->addRepositoryConfigParser(new DependencyInjection\Configuration\RepositoryAware\ActivityLogParser());
        $kernel->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $kernel->addPolicyProvider(new PolicyProvider());

        $loader = $this->getLoader($container);

        if ($container->hasExtension('ibexa_site_factory')) {
            $loader->load('services/site-factory/**/*.yaml');
        }

        if ($container->hasExtension('ibexa_fieldtype_page')) {
            $loader->load('services/page-builder/**/*.yaml');
        }
    }

    private function getLoader(ContainerBuilder $container): GlobFileLoader
    {
        $locator = new FileLocator(__DIR__ . '/Resources/config');
        $loader = new GlobFileLoader(
            $container,
            $locator,
        );
        $resolver = new LoaderResolver([new YamlFileLoader($container, $locator)]);
        $loader->setResolver($resolver);

        return $loader;
    }
}
