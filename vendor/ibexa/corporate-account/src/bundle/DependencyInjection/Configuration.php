<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\DependencyInjection;

use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('ibexa_corporate_account');

        $treeBuilder
            ->getRootNode()
                ->children()
                    ->scalarNode(CorporateAccountConfiguration::CONFIGURATION_PARENT_LOCATION_REMOTE_ID)
                        ->info('Parent Location remote ID where corporate accounts are stored')
                        ->defaultValue('corporate_account_folder')
                    ->end()
                    ->scalarNode(CorporateAccountConfiguration::CONFIGURATION_SALES_REP_USER_GROUP_REMOTE_ID)
                        ->info('Parent Location remote ID where sales representatives are stored')
                        ->defaultValue('corporate_account_sales_reps')
                    ->end()
                    ->scalarNode(CorporateAccountConfiguration::CONFIGURATION_APPLICATION_PARENT_LOCATION_REMOTE_ID)
                        ->info('Parent Location remote ID where corporate account applications are stored')
                        ->defaultValue('corporate_account_applications_folder')
                    ->end()
                    ->scalarNode(CorporateAccountConfiguration::CONFIGURATION_DEFAULT_ADMINISTRATOR_ROLE_IDENTIFIER)
                        ->info('Default Role identifier for new Applications and Companies')
                        ->defaultValue('Company admin')
                    ->end()
                    ->arrayNode(CorporateAccountConfiguration::CONFIGURATION_CONTENT_TYPE_MAPPINGS)
                        ->info('Mappings of necessary content types')
                        ->isRequired()
                        ->children()
                            ->scalarNode('member')
                                ->info('Member content type')
                                ->defaultValue('member')
                            ->end()
                            ->scalarNode('company')
                                ->info('Company content type')
                                ->defaultValue('company')
                            ->end()
                            ->scalarNode('shipping_address')
                                ->info('Shipping address content type')
                                ->defaultValue('shipping_address')
                            ->end()
                            ->scalarNode('application')
                                ->info('Application content type')
                                ->defaultValue('corporate_account_application')
                            ->end()
                            ->scalarNode('customer_portal')
                                ->info('Customer Portal content type')
                                ->defaultValue('customer_portal')
                            ->end()
                        ->end()
                    ->end()
                    ->scalarNode(CorporateAccountConfiguration::CONTENT_TYPE_GROUP)
                        ->info('Content types group identifier')
                        ->defaultValue('corporate_account')
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
