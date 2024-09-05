<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Migration\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\Configuration as SiteAccessConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration extends SiteAccessConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ibexa_migrations');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('callable_services')
                    ->info(
                        <<<INFO
                        An array of service name strings of services to be made available for type: "service", mode: "call" step.
                        You can use private services here: a dedicated container will become available for that step.
                        INFO
                    )
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('default_user_login')
                    ->defaultValue('admin')
                    ->info('Default user identifier for user context for migration commands.')
                ->end()
                ->scalarNode('default_language_code')
                    ->defaultValue('eng-GB')
                    ->info('Default language code for migration commands.')
                ->end()
                ->scalarNode('migration_directory')
                    ->defaultValue('%kernel.project_dir%/src/Migrations/Ibexa/')
                    ->info('Directory in which migration & reference files are kept.')
                ->end()
                ->scalarNode('migrations_files_subdir')
                    ->defaultValue('migrations')
                    ->info('Subdirectory in which migrations files are kept, relative to migration_directory.')
                ->end()
                ->scalarNode('references_files_subdir')
                    ->defaultValue('references')
                    ->info('Subdirectory in which files with references are kept, relative to migration_directory.')
                ->end()
                ->scalarNode('date_time_format')
                    ->defaultValue('c')
                    ->info('PHP\'s "date" function compatible format. Will be used in datetime normalization.')
                ->end()
                ->scalarNode('generator_chunk')
                    ->defaultValue(100)
                    ->info(
                        <<<INFO
                        Defines limits in query result sets when generating migrations.
                        If more than this many objects match, migration will use multiple queries to prevent memory issues.
                        INFO
                    )
                    ->validate()
                        ->ifTrue(static function ($value): bool {
                            return $value < 1;
                        })
                        ->thenInvalid('Generator chunk limit should be a positive, non-zero value')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

class_alias(Configuration::class, 'Ibexa\Platform\Bundle\Migration\DependencyInjection\Configuration');
