<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Installer\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class HandleDeprecatedProvisionerTagsCompilerPass implements CompilerPassInterface
{
    private const HEADLESS_PROVISIONER_TAG = 'ibexa.installer.provisioner.headless';
    private const DEPRECATED_CONTENT_PROVISIONER_TAG = 'ibexa.installer.provisioner.content';

    public function process(ContainerBuilder $container): void
    {
        $serviceIds = $container->findTaggedServiceIds(self::DEPRECATED_CONTENT_PROVISIONER_TAG);
        foreach ($serviceIds as $serviceId => $tags) {
            $serviceDefinition = $container->getDefinition($serviceId);

            @trigger_error(
                sprintf(
                    'Service tag `%s` is deprecated and will be removed in Ibexa DXP 5.0. Tag %s with `%s` instead.',
                    self::DEPRECATED_CONTENT_PROVISIONER_TAG,
                    $serviceId,
                    self::HEADLESS_PROVISIONER_TAG
                ),
                E_USER_DEPRECATED
            );

            if ($serviceDefinition->hasTag(self::HEADLESS_PROVISIONER_TAG)) {
                continue;
            }

            foreach ($tags as $tag) {
                $serviceDefinition->addTag(self::HEADLESS_PROVISIONER_TAG, $tag);
            }
        }
    }
}
