<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\DependencyInjection;

use Ibexa\Connect\PageBuilder\Template;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @phpstan-type BlockTemplateParameter array{
 *     label?: non-empty-string,
 *     type: non-empty-string,
 *     required: bool,
 *     options?: array<mixed>,
 * }
 *
 * @phpstan-type BlockTemplate array{
 *     label?: non-empty-string,
 *     template: non-empty-string,
 *     parameters: array<BlockTemplateParameter>,
 * }
 */
final class IbexaConnectExtension extends ConfigurableExtension implements PrependExtensionInterface
{
    public const EXTENSION_NAME = 'ibexa_connect';

    /**
     * @phpstan-param array{
     *     scenario_block: array{
     *         block_templates: array<BlockTemplate>,
     *     },
     * } $mergedConfig
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $this->addBlockTemplate($mergedConfig['scenario_block']['block_templates'] ?? [], $container);

        $loader->load('services.yaml');
    }

    /**
     * @phpstan-param array<BlockTemplate> $templates
     */
    private function addBlockTemplate(array $templates, ContainerBuilder $container): void
    {
        foreach ($templates as $key => $template) {
            $id = 'ibexa.connect.template.' . $key;
            $definition = new Definition(Template::class);
            $templateLabel = $template['label'] ?? $key;
            $definition->addTag('ibexa.connect.template', [
                'id' => $key,
                'name' => $templateLabel,
                'template' => $template['template'],
            ]);

            /** @var \Symfony\Component\DependencyInjection\Reference[] $parameters */
            $parameters = [];
            foreach ($template['parameters'] as $parameterKey => $parameter) {
                $parameterId = sprintf('%s.%s', $id, $parameterKey);

                $dynamicOptions = array_diff_key(
                    $parameter,
                    array_fill_keys(['label', 'type', 'required'], true),
                );

                $parameter = new Definition(Template\Parameter::class, [
                    '$name' => $parameterKey,
                    '$label' => $parameter['label'] ?? $parameterKey,
                    '$type' => $parameter['type'],
                    '$required' => $parameter['required'],
                    '$options' => $dynamicOptions,
                ]);

                $container->setDefinition($parameterId, $parameter);
                $parameters[] = new Reference($parameterId);
            }

            $definition->setArguments([
                '$id' => $key,
                '$label' => $templateLabel,
                '$template' => $template['template'],
                '$parameters' => $parameters,
            ]);

            $container->setDefinition($id, $definition);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependJMSTranslation($container);
    }

    private function prependJMSTranslation(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('jms_translation', [
            'configs' => [
                self::EXTENSION_NAME => [
                    'dirs' => [
                        __DIR__ . '/../../../src/',
                    ],
                    'output_dir' => __DIR__ . '/../Resources/translations/',
                    'output_format' => 'xliff',
                    'excluded_dirs' => ['Behat', 'Tests', 'node_modules'],
                    'excluded_name' => ['Configuration.php'],
                    'extractors' => [],
                ],
            ],
        ]);
    }
}
