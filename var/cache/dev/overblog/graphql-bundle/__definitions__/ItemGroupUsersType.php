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
final class ItemGroupUsersType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupUsers';
    
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
                    'type' => fn() => $services->getType('ItemGroupUsersTypes'),
                    'resolve' => fn() => [],
                    'description' => 'Content types from this group',
                ],
                'editors' => [
                    'type' => fn() => $services->getType('EditorItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "editor", 1 => $args]);
                    },
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
                'editor' => [
                    'type' => fn() => $services->getType('EditorItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "editor"]);
                    },
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the editor',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the editor',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the editor',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the editor',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the editor',
                        ],
                    ],
                ],
                'users' => [
                    'type' => fn() => $services->getType('UserItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "user", 1 => $args]);
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
                'user' => [
                    'type' => fn() => $services->getType('UserItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "user"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the user',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the user',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the user',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the user',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the user',
                        ],
                    ],
                ],
                'userGroups' => [
                    'type' => fn() => $services->getType('UserGroupItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "user_group", 1 => $args]);
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
                'userGroup' => [
                    'type' => fn() => $services->getType('UserGroupItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "user_group"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the user_group',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the user_group',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the user_group',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the user_group',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the user_group',
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