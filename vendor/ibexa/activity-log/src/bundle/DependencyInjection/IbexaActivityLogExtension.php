<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ActivityLog\DependencyInjection;

use Ibexa\Contracts\ActivityLog\ClassNameMapperInterface;
use Ibexa\Contracts\ActivityLog\CriterionMapperInterface;
use Ibexa\Contracts\ActivityLog\SortClauseMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

final class IbexaActivityLogExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(SortClauseMapperInterface::class)
            ->addTag('ibexa.activity_log.query.sort_clause_mapper');

        $container->registerForAutoconfiguration(CriterionMapperInterface::class)
            ->addTag('ibexa.activity_log.query.criterion_mapper');

        $container->registerForAutoconfiguration(ClassNameMapperInterface::class)
            ->addTag('ibexa.activity_log.class_name_mapper');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependJMSTranslation($container);

        if ($container->hasExtension('ibexa_fieldtype_page')) {
            $configFile = __DIR__ . '/../Resources/config/prepend.yaml';

            $container->addResource(new FileResource($configFile));

            $configs = Yaml::parseFile($configFile, Yaml::PARSE_CONSTANT);
            foreach ($configs as $name => $config) {
                $container->prependExtensionConfig($name, $config);
            }
        }
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                'ibexa_activity_log' => [
                    'dirs' => [
                        __DIR__ . '/../../',
                    ],
                    'excluded_dirs' => ['Behat', 'Bridge'],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                ],
            ],
        ]);
    }
}
