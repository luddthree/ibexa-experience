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
 * @phpstan-import-type TBlockConfiguration from \Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration
 */
final class BlockDefinitionConfigurationCompilerPass implements CompilerPassInterface
{
    /**
     * @deprecated since Ibexa 4.0, use
     * {@see \Ibexa\Bundle\FieldTypePage\DependencyInjection\IbexaFieldTypePageExtension::EXTENSION_NAME}
     * instead.
     */
    public const EXTENSION_CONFIG_KEY = IbexaFieldTypePageExtension::EXTENSION_NAME;
    public const BLOCK_TYPE = 'generic';

    public function process(ContainerBuilder $container)
    {
        if (!$container->has(BlockDefinitionFactory::class)) {
            return;
        }

        $factory = $container->findDefinition(BlockDefinitionFactory::class);

        $configuration = new Configuration();
        $processor = new Processor();

        $configs = $container->getExtensionConfig(IbexaFieldTypePageExtension::EXTENSION_NAME);

        $fieldTypeConfiguration = $processor->processConfiguration($configuration, $configs);

        // sort block views by priority
        /** @var array<string, TBlockConfiguration> $blocks */
        $blocks = $fieldTypeConfiguration['blocks'];
        foreach ($blocks as $identifier => $blockDefinition) {
            $blocks[$identifier]['block_type'] = self::BLOCK_TYPE;
            $blocks[$identifier]['views'] = $this->sortBlockViews($blockDefinition['views']);
        }

        $factory->addMethodCall('addConfiguration', [$blocks]);
    }

    private function sortBlockViews(array $views): array
    {
        $priority = array_column($views, 'priority');
        array_multisort($priority, SORT_DESC, SORT_NUMERIC, $views);

        return $views;
    }
}

class_alias(BlockDefinitionConfigurationCompilerPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\BlockDefinitionConfigurationCompilerPass');
