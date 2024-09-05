<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Bundle\Migration;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Ibexa\Bundle\FieldTypeRichText\IbexaFieldTypeRichTextBundle;
use Ibexa\Bundle\Migration\IbexaMigrationBundle;
use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepDenormalizer;
use Ibexa\Bundle\Migration\Serializer\Denormalizer\StepsDenormalizer;
use Ibexa\Bundle\Migration\Serializer\Normalizer\ExpressionNormalizer;
use Ibexa\Contracts\Core\Repository\Repository;
use Ibexa\Contracts\Core\Repository\SettingService;
use Ibexa\Migration\Generator\Content;
use Ibexa\Migration\Generator\ContentTypeMigrationGenerator;
use Ibexa\Migration\Generator\Role\StepBuilder\LimitationConverterManager;
use Ibexa\Migration\Generator\User;
use Ibexa\Migration\Generator\UserGroup\UserGroupMigrationGenerator;
use Ibexa\Migration\Metadata\Storage\MetadataStorage;
use Ibexa\Migration\Reference\CollectorInterface;
use Ibexa\Migration\Service\FieldTypeService as MigrationFieldTypeService;
use Ibexa\Migration\StepExecutor\ActionExecutor;
use Ibexa\Migration\StepExecutor\ActionExecutor\Section\Executor as SectionExecutor;
use Ibexa\Migration\StepExecutor\ContentType\ContentTypeFinderRegistryInterface;
use Ibexa\Migration\StepExecutor\ReferenceDefinition\Resolver;
use Ibexa\Migration\StepExecutor\RepeatableStepExecutor;
use Ibexa\Migration\StepExecutor\ServiceCallExecuteStepExecutor;
use Ibexa\Migration\StepExecutor\SettingCreateStepExecutor;
use Ibexa\Migration\StepExecutor\SettingDeleteStepExecutor;
use Ibexa\Migration\StepExecutor\SettingUpdateStepExecutor;
use Ibexa\Migration\StepExecutor\StepExecutorManager;
use Ibexa\Tests\Migration\Doubles\DummyPasswordGenerator;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Lock\Store\DoctrineDbalStore;
use Symfony\Component\Serializer\SerializerInterface;

final class IbexaTestKernel extends \Ibexa\Contracts\Core\Test\IbexaTestKernel
{
    public function registerBundles(): iterable
    {
        yield from parent::registerBundles();

        yield from [
            new IbexaMigrationBundle(),
            new IbexaFieldTypeRichTextBundle(),
        ];

        yield new DAMADoctrineTestBundle();
    }

    protected static function getExposedServicesByClass(): iterable
    {
        yield from parent::getExposedServicesByClass();
        yield Repository::class;

        yield ContentTypeMigrationGenerator::class;
        yield Content\CriterionFactory::class;
        yield User\CriterionFactory::class;

        yield ContentTypeFinderRegistryInterface::class;
        yield ActionExecutor\ContentType\Update\Executor::class;
        yield ActionExecutor\Content\Create\Executor::class;
        yield ActionExecutor\Role\Executor::class;
        yield ActionExecutor\User\Executor::class;
        yield SectionExecutor::class;
        yield ActionExecutor\UserGroup\Executor::class;
        yield CollectorInterface::class;
        yield LimitationConverterManager::class;
        yield MigrationFieldTypeService::class;
        yield ServiceCallExecuteStepExecutor::class;
        yield StepDenormalizer::class;
        yield StepsDenormalizer::class;
        yield MetadataStorage::class;

        yield SettingCreateStepExecutor::class;
        yield SettingDeleteStepExecutor::class;
        yield SettingUpdateStepExecutor::class;
        yield SettingService::class;

        yield StepExecutorManager::class;
        yield RepeatableStepExecutor::class;
        yield ExpressionNormalizer::class;

        yield UserGroupMigrationGenerator::class;
    }

    protected static function getExposedServicesById(): iterable
    {
        yield from parent::getExposedServicesById();

        yield 'ibexa.migrations.generator.content' => Content\ContentMigrationGenerator::class;
        yield 'ibexa.migrations.generator.user' => Content\ContentMigrationGenerator::class;

        yield 'ibexa.migrations.io.flysystem.default_filesystem' => FilesystemOperator::class;
        yield 'ibexa.migrations.kaliop.io.flysystem.default_filesystem' => FilesystemOperator::class;

        yield 'ibexa.migrations.reference_definition.resolver.content' => Resolver::class;
        yield 'ibexa.migrations.reference_definition.resolver.content_type' => Resolver::class;
        yield 'ibexa.migrations.reference_definition.resolver.user' => Resolver::class;
        yield 'ibexa.migrations.reference_definition.resolver.user_group' => Resolver::class;
        yield 'ibexa.migrations.reference_definition.resolver.role' => Resolver::class;
        yield 'ibexa.migrations.reference_definition.resolver.section' => Resolver::class;
        yield 'ibexa.migrations.reference_definition.resolver.language' => Resolver::class;

        yield 'ibexa.migrations.serializer' => SerializerInterface::class;

        yield 'ibexa.migrations.lock_store' => DoctrineDbalStore::class;
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        parent::registerContainerConfiguration($loader);

        $loader->load(__DIR__ . '/../integration/Resources/services.yaml');

        $loader->load(static function (ContainerBuilder $container): void {
            $config = [
                'callable_services' => ['__foo_service__'],
            ];
            $container->loadFromExtension('ibexa_migrations', $config);

            self::overwriteFilesystemAdapter($container);
            self::addFooService($container);

            $fooExpression = new Definition(\Closure::class);
            $fooExpression->setFactory([\Closure::class, 'fromCallable']);
            $fooExpression->setArguments([
                [new Reference('service_container'), 'getEnv'],
            ]);
            $fooExpression->addTag('ibexa.migrations.template.expression_language.function', [
                'function' => 'env',
            ]);
            $container->setDefinition(
                'test.ibexa.migration.template.foo_expression',
                $fooExpression,
            );

            $container->addCompilerPass(self::createCompilerPass());
        });
    }

    public function getSchemaFiles(): iterable
    {
        yield from parent::getSchemaFiles();

        yield __DIR__ . '/../integration/Resources/schema.yaml';
    }

    private static function createCompilerPass(): CompilerPassInterface
    {
        return new class() implements CompilerPassInterface {
            public function process(ContainerBuilder $container): void
            {
                self::overwritePasswordGenerator($container);
            }

            private static function overwritePasswordGenerator(ContainerBuilder $container): void
            {
                $passwordGeneratorDefinition = new Definition(DummyPasswordGenerator::class);
                $container->setDefinition('test.ibexa.migrations.password_generator.dummy', $passwordGeneratorDefinition);

                $userStepBuilderCreateDefinition = $container->getDefinition(User\StepBuilder\Create::class);
                $userStepBuilderCreateDefinition->setArgument(1, new Reference('test.ibexa.migrations.password_generator.dummy'));
            }
        };
    }

    private static function overwriteFilesystemAdapter(ContainerBuilder $container): void
    {
        $container->setParameter('io_root_dir', '%kernel.project_dir%/var/io_root');

        $definition = new Definition(InMemoryFilesystemAdapter::class);
        $definition->setPublic(true);
        $container->setDefinition(
            self::getAliasServiceId('ibexa.migrations.flysystem_memory_adapter'),
            $definition
        );

        $container->setAlias(
            'ibexa.migrations.io.flysystem.default_adapter',
            self::getAliasServiceId('ibexa.migrations.flysystem_memory_adapter')
        );

        $definition = new Definition(InMemoryFilesystemAdapter::class);
        $definition->setPublic(true);
        $container->setDefinition(
            self::getAliasServiceId('ibexa.migrations.kaliop.flysystem_memory_adapter'),
            $definition
        );

        $container->setAlias(
            'ibexa.migrations.kaliop.io.flysystem.default_adapter',
            self::getAliasServiceId('ibexa.migrations.flysystem_memory_adapter')
        );
    }

    private static function addFooService(ContainerBuilder $container): void
    {
        $container->setDefinition('__foo_service__', new Definition(FooService::class));
        $container->setAlias(static::getAliasServiceId('__foo_service__'), '__foo_service__')->setPublic(true);
    }
}

class_alias(IbexaTestKernel::class, 'Ibexa\Platform\Tests\Bundle\Migration\IbexaTestKernel');
