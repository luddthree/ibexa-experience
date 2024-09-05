<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration;
use Ibexa\Bundle\FieldTypePage\DependencyInjection\IbexaFieldTypePageExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class AbstractConfigurationAwareCompilerPass
{
    /**
     * @deprecated since Ibexa 4.0, use
     * {@see \Ibexa\Bundle\FieldTypePage\DependencyInjection\IbexaFieldTypePageExtension::EXTENSION_NAME}
     * instead.
     */
    public const EXTENSION_CONFIG_KEY = IbexaFieldTypePageExtension::EXTENSION_NAME;

    /**
     * Returns processed configuration for the extension.
     *
     * It adds defaults values if there is incomplete structure coming from config file.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return array
     */
    protected function fetchConfiguration(ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        $config = [];
        foreach ($container->getExtensionConfig(IbexaFieldTypePageExtension::EXTENSION_NAME) as $subConfig) {
            $config = array_merge_recursive($config, $subConfig);
        }

        $processedConfig = $processor->processConfiguration($configuration, [static::EXTENSION_CONFIG_KEY => $config]);

        foreach ($processedConfig['blocks'] as $blockType => $blockProperties) {
            $processedConfig['blocks'][$blockType]['type'] = $blockType;

            if (isset($blockProperties['attributes'])) {
                foreach ($blockProperties['attributes'] as $attributeId => $attributeProperties) {
                    $processedConfig['blocks'][$blockType]['attributes'][$attributeId]['id'] = $attributeId;
                }
            }
        }

        return $processedConfig;
    }
}

class_alias(AbstractConfigurationAwareCompilerPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\AbstractConfigurationAwareCompilerPass');
