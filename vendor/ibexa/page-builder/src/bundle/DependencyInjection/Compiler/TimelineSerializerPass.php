<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\PageBuilder\DependencyInjection\Compiler;

use Ibexa\Bundle\PageBuilder\DependencyInjection\Configuration;
use Ibexa\Bundle\PageBuilder\DependencyInjection\IbexaPageBuilderExtension;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TimelineSerializerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig(IbexaPageBuilderExtension::EXTENSION_NAME);

        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $container->setParameter(
            'ibexa.page_builder.timeline.serializer.event_type_map',
            $config['timeline']['serializer']['event_type_map']
        );

        $container->setParameter(
            'ibexa.page_builder.timeline.serializer.metadata_dirs',
            $config['timeline']['serializer']['metadata_dirs']
        );
    }
}

class_alias(TimelineSerializerPass::class, 'EzSystems\EzPlatformPageBuilderBundle\DependencyInjection\Compiler\TimelineSerializerPass');
