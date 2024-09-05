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
final class ItemGroupMediaType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupMedia';
    
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
                    'type' => fn() => $services->getType('ItemGroupMediaTypes'),
                    'resolve' => fn() => [],
                    'description' => 'Content types from this group',
                ],
                'files' => [
                    'type' => fn() => $services->getType('FileItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "file", 1 => $args]);
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
                'file' => [
                    'type' => fn() => $services->getType('FileItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "file"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the file',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the file',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the file',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the file',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the file',
                        ],
                    ],
                ],
                'images' => [
                    'type' => fn() => $services->getType('ImageItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "image", 1 => $args]);
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
                'image' => [
                    'type' => fn() => $services->getType('ImageItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "image"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the image',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the image',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the image',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the image',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the image',
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