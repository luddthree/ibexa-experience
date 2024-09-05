<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Dashboard;

use Ibexa\Bundle\Dashboard\DependencyInjection\Parser\Dashboard;
use Ibexa\Dashboard\Security\DashboardPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaDashboardBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $coreExtension */
        $coreExtension = $container->getExtension('ibexa');
        $coreExtension->addConfigParser(new Dashboard());
        $coreExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $coreExtension->addPolicyProvider(new DashboardPolicyProvider());
    }
}
