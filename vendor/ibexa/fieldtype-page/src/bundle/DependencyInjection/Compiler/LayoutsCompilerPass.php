<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler;

use Ibexa\FieldTypePage\Registry\LayoutDefinitionRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritdoc}
 */
class LayoutsCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(LayoutDefinitionRegistry::class)) {
            return;
        }

        $registry = $container->findDefinition(LayoutDefinitionRegistry::class);
        $layouts = $container->findTaggedServiceIds('ibexa.field_type.page.layout');

        foreach ($layouts as $id => $layout) {
            $registry->addMethodCall('addLayoutDefinition', [new Reference($id)]);
        }
    }
}

class_alias(LayoutsCompilerPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\LayoutsCompilerPass');
