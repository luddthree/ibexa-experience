<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\VersionComparison\DependencyInjection;

use Ibexa\Bundle\VersionComparison\DependencyInjection\Compiler\HtmlDiffHandlerCompilerPass;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(IbexaVersionComparisonExtension::EXTENSION_NAME);

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->arrayNode('html')
                ->addDefaultsIfNotSet()
                ->children()
                    ->enumNode('method')
                        ->values([
                            HtmlDiffHandlerCompilerPass::DEFAULT_METHOD,
                            HtmlDiffHandlerCompilerPass::EXTERNAL_TOOL_METHOD,
                            HtmlDiffHandlerCompilerPass::PLAIN_TEXT_METHOD,
                        ])
                        ->defaultValue(HtmlDiffHandlerCompilerPass::DEFAULT_METHOD)
                        ->info('Select method to use for comparing of html strings')
                    ->end()
                    ->scalarNode('external_tool_path')
                        ->defaultValue('')
                        ->info('Provide path to external html comparison tool')
                    ->end()
                    ->scalarNode('timeout')
                        ->defaultValue(60)
                        ->info('Set timeout for comparison tool')
                    ->end()
                    ->arrayNode('additional_parameters')
                        ->info('Additional parameters that should be passed into external tool')
                        ->defaultValue([])
                        ->scalarPrototype()->end()
                    ->end()
                    ->scalarNode('path_to_template')
                        ->info('Path to template where block used for rendering plain text html comparison is placed')
                        ->defaultValue('@IbexaVersionComparison/themes/admin/version_comparison/comparison_result_blocks.html.twig')
                    ->end()
                    ->scalarNode('block_name')
                        ->info('Block name used for rendering plain text html comparison')
                        ->defaultValue('string_diff_render')
                    ->end()
                ->end()
            ->end()
        ->end()
        ;

        return $treeBuilder;
    }
}

class_alias(Configuration::class, 'EzSystems\EzPlatformVersionComparisonBundle\DependencyInjection\Configuration');
