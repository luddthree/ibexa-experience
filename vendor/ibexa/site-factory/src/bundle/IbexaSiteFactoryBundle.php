<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory;

use Ibexa\Bundle\SiteFactory\DependencyInjection\Compiler\CriteriaConverterPass;
use Ibexa\Bundle\SiteFactory\DependencyInjection\Parser\Pagination;
use Ibexa\Bundle\SiteFactory\DependencyInjection\Parser\SiteFactory;
use Ibexa\SiteFactory\Security\SiteFactoryPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaSiteFactoryBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ibexa');
        $kernelExtension->addPolicyProvider(new SiteFactoryPolicyProvider());
        $kernelExtension->addConfigParser(new SiteFactory());
        $kernelExtension->addConfigParser(new Pagination());
        $kernelExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $this->addCompilerPasses($container);
    }

    private function addCompilerPasses(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new CriteriaConverterPass());
    }
}

class_alias(IbexaSiteFactoryBundle::class, 'EzSystems\EzPlatformSiteFactoryBundle\EzPlatformSiteFactoryBundle');
