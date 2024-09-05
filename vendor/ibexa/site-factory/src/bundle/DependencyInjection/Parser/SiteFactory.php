<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\DependencyInjection\Parser;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\AbstractParser;
use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

/**
 * Configuration parser for Site Factory.
 *
 * Example configuration:
 * ```yaml
 * ezplatform:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          site_factory:
 *              sites_location_id: 55
 * ```
 */
class SiteFactory extends AbstractParser
{
    public const DEFAULT_SITES_LOCATION_ID = 2;
    public const DEFAULT_SITE_SKELETONS_LOCATION_ID = 56;

    /**
     * {@inheritdoc}
     */
    public function addSemanticConfig(NodeBuilder $nodeBuilder): void
    {
        $nodeBuilder
            ->arrayNode('site_factory')
                ->info('Site Factory configuration')
                ->children()
                    ->scalarNode('sites_location_id')
                        ->info('Root Location ID for the sites')
                        ->defaultValue(self::DEFAULT_SITES_LOCATION_ID)
                    ->end()
                    ->scalarNode('site_skeletons_location_id')
                        ->info('Root Location ID for the site skeletons')
                        ->defaultValue(self::DEFAULT_SITE_SKELETONS_LOCATION_ID)
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['site_factory'])) {
            return;
        }

        $settings = $scopeSettings['site_factory'];

        if (!empty($settings['sites_location_id'])) {
            $contextualizer->setContextualParameter(
                'site_factory.sites_location_id',
                $currentScope,
                $settings['sites_location_id']
            );
        }

        if (!empty($settings['site_skeletons_location_id'])) {
            $contextualizer->setContextualParameter(
                'site_factory.site_skeletons_location_id',
                $currentScope,
                $settings['site_skeletons_location_id']
            );
        }
    }
}

class_alias(SiteFactory::class, 'EzSystems\EzPlatformSiteFactoryBundle\DependencyInjection\Parser\SiteFactory');
