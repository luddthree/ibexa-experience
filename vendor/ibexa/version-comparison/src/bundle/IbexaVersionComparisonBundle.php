<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison;

use Ibexa\Bundle\VersionComparison\DependencyInjection\Compiler\HtmlDiffHandlerCompilerPass;
use Ibexa\Bundle\VersionComparison\DependencyInjection\Configuration\Parser\FieldComparisonTemplates;
use Ibexa\VersionComparison\Security\ComparisonPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaVersionComparisonBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $core */
        $core = $container->getExtension('ibexa');
        $core->addPolicyProvider(new ComparisonPolicyProvider());

        $core->addConfigParser(new FieldComparisonTemplates());

        $container->addCompilerPass(new HtmlDiffHandlerCompilerPass());
    }
}

class_alias(IbexaVersionComparisonBundle::class, 'EzSystems\EzPlatformVersionComparisonBundle\EzPlatformVersionComparisonBundle');
