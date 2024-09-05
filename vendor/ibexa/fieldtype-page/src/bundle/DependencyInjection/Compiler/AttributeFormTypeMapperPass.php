<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler;

use Ibexa\FieldTypePage\FieldType\Page\Registry\AttributeFormTypeMapperRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * {@inheritdoc}
 */
class AttributeFormTypeMapperPass extends AbstractConfigurationAwareCompilerPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(AttributeFormTypeMapperRegistry::class)) {
            return;
        }

        $registry = $container->findDefinition(AttributeFormTypeMapperRegistry::class);
        $mapperDefinitions = $container->findTaggedServiceIds('ibexa.page_builder.form_type_attribute.mapper');

        foreach ($mapperDefinitions as $id => $tags) {
            foreach ($tags as $attributes) {
                $registry->addMethodCall('addMapper', [$attributes['alias'], new Reference($id)]);
            }
        }
    }
}

class_alias(AttributeFormTypeMapperPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\AttributeFormTypeMapperPass');
