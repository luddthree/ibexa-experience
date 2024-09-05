<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Personalization;

use Ibexa\Bundle\Personalization\DependencyInjection\Compiler\RestResponsePass;
use Ibexa\Bundle\Personalization\DependencyInjection\Configuration\Parser\Personalization;
use Ibexa\Personalization\Security\PersonalizationPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaPersonalizationBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $extension */
        $extension = $container->getExtension('ibexa');
        $extension->addPolicyProvider(new PersonalizationPolicyProvider());
        $extension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);
        $extension->addConfigParser(new Personalization());
        $container->addCompilerPass(new RestResponsePass());
    }
}

class_alias(IbexaPersonalizationBundle::class, 'Ibexa\Platform\Bundle\Personalization\IbexaPlatformPersonalizationBundle');
