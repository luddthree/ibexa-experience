<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount;

use Ibexa\Bundle\CorporateAccount\DependencyInjection\Compiler\AdminUiFormCompilerPass;
use Ibexa\Bundle\CorporateAccount\DependencyInjection\Configuration\Parser\CorporateAccounts;
use Ibexa\CorporateAccount\Permission\Policy\PolicyProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaCorporateAccountBundle extends Bundle
{
    public const CUSTOMER_PORTAL_GROUP_NAME = 'corporate_group';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/Resources/config')
        );

        if ($container->hasExtension('ibexa_commerce_eshop')) {
            $loader->load('services/commerce_bridge.yaml');
        }

        if ($container->hasExtension('ibexa_cdp')) {
            $loader->load('services/cdp/item_processors.yaml');
        }

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ibexa');

        $kernelExtension->addConfigParser(new CorporateAccounts());
        $kernelExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $kernelExtension->addPolicyProvider(new PolicyProvider());

        $container->addCompilerPass(new AdminUiFormCompilerPass());
    }
}
