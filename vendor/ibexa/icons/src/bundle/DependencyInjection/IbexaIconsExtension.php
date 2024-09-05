<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Icons\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class IbexaIconsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }
}

class_alias(IbexaIconsExtension::class, 'Ibexa\Platform\Bundle\Icons\DependencyInjection\IbexaPlatformIconsExtension');
