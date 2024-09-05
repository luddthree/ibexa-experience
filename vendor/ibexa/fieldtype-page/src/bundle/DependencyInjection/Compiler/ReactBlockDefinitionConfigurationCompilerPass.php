<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration;
use Ibexa\Bundle\FieldTypePage\DependencyInjection\IbexaFieldTypePageExtension;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinitionFactory;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @phpstan-import-type TReactBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
final class ReactBlockDefinitionConfigurationCompilerPass implements CompilerPassInterface
{
    public const BLOCK_TYPE = 'react';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(BlockDefinitionFactory::class)) {
            return;
        }

        $factory = $container->findDefinition(BlockDefinitionFactory::class);

        $configuration = new Configuration();
        $processor = new Processor();

        $configs = $container->getExtensionConfig(IbexaFieldTypePageExtension::EXTENSION_NAME);

        $fieldTypeConfiguration = $processor->processConfiguration($configuration, $configs);

        /** @var array<string, TReactBlockConfiguration> $blocks */
        $blocks = $fieldTypeConfiguration[Configuration::KEY_REACT_BLOCKS];
        foreach ($blocks as $identifier => $blockDefinition) {
            $blocks[$identifier]['block_type'] = self::BLOCK_TYPE;
            $blocks[$identifier]['views'] = [
                'default' => [
                    'name' => 'Default',
                    'template' => '@IbexaFieldTypePage/blocks/react_block.html.twig',
                    'priority' => -255,
                    'options' => [],
                ],
            ];
        }

        $factory->addMethodCall('addConfiguration', [$blocks]);
    }
}
