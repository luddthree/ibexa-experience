<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Connect;

use Ibexa\Bundle\Connect\DependencyInjection\Compiler\BlockCompilerPass;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaConnectBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/Resources/config')
        );

        if ($container->hasExtension('ibexa_form_builder')) {
            $loader->load('bridge/form_builder.yaml');
        }

        if ($container->hasExtension('ibexa_fieldtype_page')) {
            $loader->load('bridge/page_builder.yaml');
            $container->addCompilerPass(new BlockCompilerPass());
        }
    }
}
