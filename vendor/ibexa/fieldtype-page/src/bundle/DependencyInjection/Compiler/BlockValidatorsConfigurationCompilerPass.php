<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler;

use Ibexa\Bundle\FieldTypePage\DependencyInjection\Configuration;
use Ibexa\Bundle\FieldTypePage\DependencyInjection\IbexaFieldTypePageExtension;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintClassRegistry;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BlockValidatorsConfigurationCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(ConstraintClassRegistry::class)) {
            return;
        }

        $registry = $container->findDefinition(ConstraintClassRegistry::class);

        $configuration = new Configuration();
        $processor = new Processor();

        $configs = $container->getExtensionConfig(IbexaFieldTypePageExtension::EXTENSION_NAME);

        $fieldTypeConfiguration = $processor->processConfiguration($configuration, $configs);

        $blockValidators = $fieldTypeConfiguration['block_validators'];
        $registry->addMethodCall('setConstraintClasses', [$blockValidators]);
    }
}

class_alias(BlockValidatorsConfigurationCompilerPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\BlockValidatorsConfigurationCompilerPass');
