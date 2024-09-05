<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Bundle\Fastly\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        return new TreeBuilder(IbexaFastlyExtension::EXTENSION_NAME);
    }
}

class_alias(Configuration::class, 'EzSystems\PlatformFastlyCacheBundle\DependencyInjection\Configuration');
