<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\DependencyInjection\Compiler;

use Ibexa\ProductCatalog\Local\Persistence\Legacy\Attribute\Inheritance\Gateway;
use LogicException;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class AttributeStorageGatewayCompilerPass implements CompilerPassInterface
{
    private const STORAGE_DEFINITION_TAG = 'ibexa.product_catalog.attribute.storage_definition';

    public function process(ContainerBuilder $container): void
    {
        $storageServiceIds = $container->findTaggedServiceIds(self::STORAGE_DEFINITION_TAG);
        foreach ($storageServiceIds as $serviceId => $tags) {
            $discriminators = [];
            foreach ($tags as $tag) {
                if (!isset($tag['type'])) {
                    continue;
                }

                $discriminator = $tag['type'];
                $discriminators[] = $discriminator;

                if (isset($discriminators[$discriminator])) {
                    throw new LogicException(sprintf(
                        'Unable to register service "%s" with tag "%s" (type: "%s") because another '
                        . 'service with this tag & type exists (service ID: "%s").',
                        $serviceId,
                        self::STORAGE_DEFINITION_TAG,
                        $discriminator,
                        $discriminators[$discriminator],
                    ));
                }
            }

            $definitionId = sprintf(
                'ibexa.product_catalog.attribute.gateway.%s',
                implode('_', $discriminators),
            );

            if ($container->hasDefinition($definitionId)) {
                throw new LogicException(sprintf(
                    'Unable to create gateway service definition. A service with id "%s" already exists',
                    $definitionId,
                ));
            }

            $gatewayDefinition = new Definition(Gateway::class, [
                '$connection' => new Reference('ibexa.persistence.connection'),
                '$storageConverters' => new TaggedIteratorArgument(
                    'ibexa.product_catalog.attribute.storage_converter',
                    'type',
                ),
                '$storageDefinition' => new Reference($serviceId),
                '$discriminators' => $discriminators,
            ]);
            $gatewayDefinition->setAutowired(true);
            $gatewayDefinition->setAutoconfigured(true);
            $gatewayDefinition->addTag('ibexa.product_catalog.attribute.gateway');

            $container->setDefinition(
                $definitionId,
                $gatewayDefinition,
            );
        }
    }
}
