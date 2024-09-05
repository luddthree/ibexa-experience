<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\FieldTypePage\DependencyInjection\Compiler\GraphQL;

use Ibexa\FieldTypePage\GraphQL\Schema\BlockAttributeBuilderRegistry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BlockAttributesBuildersPass implements CompilerPassInterface
{
    private const TAG = 'ibexa.graphql.field_type.page.block_attribute_builder';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(BlockAttributeBuilderRegistry::class)) {
            return;
        }

        $definition = $container->findDefinition(BlockAttributeBuilderRegistry::class);
        $taggedServices = $container->findTaggedServiceIds(self::TAG);

        $builders = [];
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    throw new \InvalidArgumentException(
                        "The ezplatform_graphql.field_value_builder tag requires a 'type' property set to the Field Type's identifier"
                    );
                }

                $builders[$tag['type']] = new Reference($id);
            }
        }

        $definition->addMethodCall('setAttributeBuilders', [$builders]);
    }
}

class_alias(BlockAttributesBuildersPass::class, 'EzSystems\EzPlatformPageFieldTypeBundle\DependencyInjection\Compiler\GraphQL\BlockAttributesBuildersPass');
