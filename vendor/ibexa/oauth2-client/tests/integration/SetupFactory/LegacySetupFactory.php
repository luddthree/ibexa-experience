<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\OAuth2Client\SetupFactory;

use Ibexa\Contracts\Core\Test\Repository\SetupFactory\Legacy as CoreLegacySetupFactory;
use Ibexa\Core\Base\Container\Compiler;
use RuntimeException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class LegacySetupFactory extends CoreLegacySetupFactory
{
    protected function externalBuildContainer(ContainerBuilder $containerBuilder): void
    {
        parent::externalBuildContainer($containerBuilder);

        $this->loadCoreSettings($containerBuilder);
        $this->loadOAuthClientSettings($containerBuilder);
    }

    /**
     * Load Ibexa DXP Kernel settings and setup container.
     */
    protected function loadCoreSettings(ContainerBuilder $containerBuilder): void
    {
        $kernelRootDir = realpath(__DIR__ . '/../../../vendor/ibexa/core');
        if (false === $kernelRootDir) {
            throw new RuntimeException('Unable to find the ezplatform-kernel package directory');
        }

        $loader = new YamlFileLoader(
            $containerBuilder,
            new FileLocator([
                "{$kernelRootDir}/src/lib/Resources/settings",
                "{$kernelRootDir}/tests/integration/Core/Resources/settings",
            ])
        );

        $loader->load('fieldtype_external_storages.yml');
        $loader->load('fieldtype_services.yml');
        $loader->load('fieldtypes.yml');
        $loader->load('indexable_fieldtypes.yml');
        $loader->load('io.yml');
        $loader->load('repository.yml');
        $loader->load('repository/inner.yml');
        $loader->load('repository/event.yml');
        $loader->load('repository/siteaccessaware.yml');
        $loader->load('repository/autowire.yml');
        $loader->load('roles.yml');
        $loader->load('storage_engines/common.yml');
        $loader->load('storage_engines/cache.yml');
        $loader->load('storage_engines/legacy.yml');
        $loader->load('storage_engines/shortcuts.yml');
        $loader->load('search_engines/common.yml');
        $loader->load('settings.yml');
        $loader->load('thumbnails.yml');
        $loader->load('utils.yml');
        $loader->load('policies.yml');

        $loader->load('search_engines/legacy.yml');

        $loader->load('integration_legacy.yml');
        $loader->load('common.yml');

        $containerBuilder->setParameter('ibexa.kernel.root_dir', realpath($kernelRootDir));

        $containerBuilder->addCompilerPass(new Compiler\FieldTypeRegistryPass());
        $containerBuilder->addCompilerPass(new Compiler\Persistence\FieldTypeRegistryPass());
        $containerBuilder->addCompilerPass(new Compiler\Storage\ExternalStorageRegistryPass());
        $containerBuilder->addCompilerPass(new Compiler\Storage\Legacy\FieldValueConverterRegistryPass());
        $containerBuilder->addCompilerPass(new Compiler\Storage\Legacy\RoleLimitationConverterPass());
        $containerBuilder->addCompilerPass(new Compiler\Search\Legacy\CriteriaConverterPass());
        $containerBuilder->addCompilerPass(new Compiler\Search\Legacy\CriterionFieldValueHandlerRegistryPass());
        $containerBuilder->addCompilerPass(new Compiler\Search\Legacy\SortClauseConverterPass());
        $containerBuilder->addCompilerPass(new Compiler\Search\FieldRegistryPass());

        $containerBuilder->setParameter('ibexa.persistence.legacy.dsn', self::$dsn);
        $containerBuilder->setParameter('ibexa.io.dir.root', self::$ioRootDir . '/');
    }

    private function loadOAuthClientSettings(ContainerBuilder $containerBuilder): void
    {
        $settingsPath = realpath(__DIR__ . '/../../../src/bundle/Resources/config/');
        if (false === $settingsPath) {
            throw new RuntimeException('Unable to find package settings');
        }

        // load core settings
        $loader = new YamlFileLoader($containerBuilder, new FileLocator($settingsPath));
        $loader->load('services/repository.yaml');
    }
}
class_alias(LegacySetupFactory::class, 'Ibexa\Platform\Tests\Integration\OAuth2Client\SetupFactory\LegacySetupFactory');
