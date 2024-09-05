<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\DependencyInjection;

use Faker\Factory as FakerFactory;
use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepNormalizerInterface;
use Ibexa\Bundle\Migration\Serializer\Normalizer\Step\ServiceCallStepNormalizer;
use Ibexa\Contracts\Migration\Serializer\Denormalizer\ActionDenormalizerInterface;
use Ibexa\Contracts\Migration\Serializer\Normalizer\ActionNormalizerInterface;
use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderInterface;
use Ibexa\Migration\StepExecutor\ServiceCallExecuteStepExecutor;
use Ibexa\Migration\StepExecutor\StepExecutorInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IbexaMigrationExtension extends Extension
{
    /**
     * @param array<mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ibexa.migrations.default.user_login', $config['default_user_login']);
        $container->setParameter('ibexa.migrations.default.language_code', $config['default_language_code']);
        $container->setParameter('ibexa.migrations.default.migration_path', $config['migration_directory']);
        $container->setParameter('ibexa.migrations.default.migrations_files_subdir', $config['migrations_files_subdir']);
        $container->setParameter('ibexa.migrations.default.references_files_subdir', $config['references_files_subdir']);
        $container->setParameter('ibexa.migrations.default.date_time.format', $config['date_time_format']);
        $container->setParameter('ibexa.migrations.default.generator.chunk_size', $config['generator_chunk']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        if (class_exists(FakerFactory::class)) {
            $loader->load('services/faker.yaml');
        }

        $this->setDefinitionFilePathParameter($container);

        $container->registerForAutoconfiguration(StepNormalizerInterface::class)
            ->addTag('ibexa.migrations.serializer.step_normalizer');

        $container->registerForAutoconfiguration(StepExecutorInterface::class)
            ->addTag('ibexa.migrations.step_executor');

        $container->registerForAutoconfiguration(ActionDenormalizerInterface::class)
            ->addTag('ibexa.migrations.serializer.normalizer');

        $container->registerForAutoconfiguration(ActionNormalizerInterface::class)
            ->addTag('ibexa.migrations.serializer.normalizer');

        $container->registerForAutoconfiguration(ContentTypeFinderInterface::class)
            ->addTag('ibexa.migrations.step_executor.content_type.finder');

        $callableServices = [];
        foreach ($config['callable_services'] ?? [] as $callableService) {
            $callableServices[$callableService] = new Reference($callableService);
        }

        $callableServicesContainer = ServiceLocatorTagPass::register($container, $callableServices);
        $container->getDefinition(ServiceCallStepNormalizer::class)
            ->addArgument($callableServicesContainer);
        $container->getDefinition(ServiceCallExecuteStepExecutor::class)
            ->addArgument($callableServicesContainer);

        if ($this->shouldLoadBehatTestServices($container)) {
            $loader->load('test/feature_contexts.yaml');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'ibexa_migrations';
    }

    private function setDefinitionFilePathParameter(ContainerBuilder $container): void
    {
        $container->setParameter(
            'ibexa.migrations.serializer.by_definition_normalizer.definition_file_path',
            dirname(__DIR__) . '/Resources/config/migration_definition/definitions.yaml'
        );
    }

    private function shouldLoadBehatTestServices(ContainerBuilder $container): bool
    {
        return $container->hasParameter('ibexa.behat.browser.enabled')
            && true === $container->getParameter('ibexa.behat.browser.enabled');
    }
}

class_alias(IbexaMigrationExtension::class, 'Ibexa\Platform\Bundle\Migration\DependencyInjection\IbexaPlatformMigrationExtension');
