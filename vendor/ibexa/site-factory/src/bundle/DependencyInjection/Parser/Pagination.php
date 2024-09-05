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
 * Configuration parser for pagination limits declaration.
 *
 * Example configuration:
 * ```yaml
 * ezpublish:
 *   system:
 *      default: # configuration per siteaccess or siteaccess group
 *          pagination_site_factory:
 *              sites_limit: 10
 * ```
 */
class Pagination extends AbstractParser
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('pagination_site_factory')
                ->info('site factory related pagination configuration')
                ->children()
                    ->scalarNode('sites_limit')->isRequired()->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer): void
    {
        if (empty($scopeSettings['pagination_site_factory'])) {
            return;
        }

        $settings = $scopeSettings['pagination_site_factory'];
        $keys = [
            'sites_limit',
        ];

        foreach ($keys as $key) {
            if (!isset($settings[$key]) || empty($settings[$key])) {
                continue;
            }

            $contextualizer->setContextualParameter(
                sprintf('pagination_site_factory.%s', $key),
                $currentScope,
                $settings[$key]
            );
        }
    }
}

class_alias(Pagination::class, 'EzSystems\EzPlatformSiteFactoryBundle\DependencyInjection\Parser\Pagination');
