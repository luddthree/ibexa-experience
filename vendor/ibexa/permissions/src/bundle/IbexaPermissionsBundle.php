<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Permissions;

use Ibexa\Bundle\Permissions\DependencyInjection\IbexaPermissionsExtension;
use Ibexa\Permissions\Security\FieldGroupLimitationPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class IbexaPermissionsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $kernelExtension */
        $kernelExtension = $container->getExtension('ibexa');
        $kernelExtension->addPolicyProvider(new FieldGroupLimitationPolicyProvider());
    }

    /**
     * Overwritten to be able to use custom alias.
     */
    public function getContainerExtension(): ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new IbexaPermissionsExtension();
        }

        return $this->extension;
    }
}

class_alias(IbexaPermissionsBundle::class, 'Ibexa\Platform\Bundle\Permissions\PlatformPermissionsBundle');
