<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect\DependencyInjection\Compiler;

use Ibexa\Bundle\Connect\EventSubscriber\PageBuilderPreRenderEventSubscriber;
use Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\BlockDefinitionConfigurationCompilerPass;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @phpstan-import-type TBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
final class BlockCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(BlockDefinitionFactory::class)) {
            return;
        }

        $serviceIds = $container->findTaggedServiceIds('ibexa.connect.template');

        $views = [];
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['id'], $tag['name'], $tag['template'])) {
                    $diff = array_diff_key(['id' => true, 'name' => true, 'template' => true], $tag);
                    $message = sprintf(
                        'Service "%s" is misconfigured: tag "%s" does not contain attributes: "%s".',
                        $serviceId,
                        'ibexa.connect.template',
                        implode('", "', $diff),
                    );
                    $container->log($this, $message);

                    continue;
                }

                $id = (string)$tag['id'];
                $name = (string)$tag['name'];
                $template = (string)$tag['template'];
                $priority = (int)($tag['priority'] ?? 0);
                $views[$id] = [
                    'template' => $template,
                    'name' => $name,
                    'priority' => $priority,
                ];
            }
        }
        $configuration = $this->getBlockConfiguration($views);

        $definition = $container->getDefinition(BlockDefinitionFactory::class);
        $definition->addMethodCall(
            'addConfiguration',
            [
                ['ibexa_connect_block' => $configuration],
            ],
        );
    }

    /**
     * @phpstan-param array<string, array{template: string, name: string, priority: int}> $views
     *
     * @phpstan-return TBlockConfiguration
     */
    private function getBlockConfiguration(array $views): array
    {
        return [
            'identifier' => PageBuilderPreRenderEventSubscriber::IBEXA_CONNECT_BLOCK,
            'block_type' => BlockDefinitionConfigurationCompilerPass::BLOCK_TYPE,
            'name' => 'Ibexa Connect',
            'category' => null,
            'thumbnail' => '/bundles/ibexaicons/img/all-icons.svg#connect',
            'visible' => true,
            'configuration_template' => '@IbexaPageBuilder/page_builder/block/config.html.twig',
            'views' => $views,
            'attributes' => [
                'url' => [
                    'name' => 'Webhook link',
                    'type' => 'url',
                    'category' => 'default',
                    'validators' => [
                        'not_blank' => [
                            'message' => 'You must provide value',
                        ],
                        'url' => [],
                    ],
                ],
            ],
        ];
    }
}
