<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Connector\Dam;

use Ibexa\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser\DamConfigurationParser;
use Ibexa\Bundle\Connector\Dam\DependencyInjection\Configuration\Parser\ImageAssetView;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaConnectorDamBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');

        $core->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $core->addConfigParser(new ImageAssetView());
        $core->addConfigParser(new DamConfigurationParser());
    }
}

class_alias(IbexaConnectorDamBundle::class, 'Ibexa\Platform\Bundle\Connector\Dam\IbexaPlatformConnectorDamBundle');
