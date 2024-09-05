<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ItemGroupCustomerPortalType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupCustomerPortal';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_info' => [
                    'type' => fn() => $services->getType('ContentTypeGroup'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                    'description' => 'The contenttype group\'s properties',
                ],
                '_types' => [
                    'type' => fn() => $services->getType('ItemGroupCustomerPortalTypes'),
                    'resolve' => fn() => [],
                    'description' => 'Content types from this group',
                ],
                'customerPortals' => [
                    'type' => fn() => $services->getType('CustomerPortalItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "customer_portal", 1 => $args]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'after',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'first',
                            'type' => Type::int(),
                        ],
                        [
                            'name' => 'before',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'last',
                            'type' => Type::int(),
                        ],
                        [
                            'name' => 'query',
                            'type' => fn() => $services->getType('ContentSearchQuery'),
                            'description' => 'A Content query used to filter results',
                        ],
                        [
                            'name' => 'sortBy',
                            'type' => fn() => Type::listOf($services->getType('SortByOptions')),
                            'description' => 'A Sort Clause, or array of Clauses. Add _desc after a Clause to reverse it',
                        ],
                    ],
                ],
                'customerPortal' => [
                    'type' => fn() => $services->getType('CustomerPortalItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "customer_portal"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the customer_portal',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the customer_portal',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the customer_portal',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the customer_portal',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the customer_portal',
                        ],
                    ],
                ],
                'customerPortalPages' => [
                    'type' => fn() => $services->getType('CustomerPortalPageItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "customer_portal_page", 1 => $args]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'after',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'first',
                            'type' => Type::int(),
                        ],
                        [
                            'name' => 'before',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'last',
                            'type' => Type::int(),
                        ],
                        [
                            'name' => 'query',
                            'type' => fn() => $services->getType('ContentSearchQuery'),
                            'description' => 'A Content query used to filter results',
                        ],
                        [
                            'name' => 'sortBy',
                            'type' => fn() => Type::listOf($services->getType('SortByOptions')),
                            'description' => 'A Sort Clause, or array of Clauses. Add _desc after a Clause to reverse it',
                        ],
                    ],
                ],
                'customerPortalPage' => [
                    'type' => fn() => $services->getType('CustomerPortalPageItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "customer_portal_page"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the customer_portal_page',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the customer_portal_page',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the customer_portal_page',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the customer_portal_page',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the customer_portal_page',
                        ],
                    ],
                ],
            ],
        ];
        
        parent::__construct($configProcessor->process($config));
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getAliases(): array
    {
        return [self::NAME];
    }
}