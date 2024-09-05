<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Installer;

use Ibexa\Bundle\Installer\DependencyInjection\Compiler\HandleDeprecatedProvisionerTagsCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaInstallerBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HandleDeprecatedProvisionerTagsCompilerPass());
    }
}

class_alias(IbexaInstallerBundle::class, 'Ibexa\Platform\Bundle\Installer\IbexaPlatformInstallerBundle');
