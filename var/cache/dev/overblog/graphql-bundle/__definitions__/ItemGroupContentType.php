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
final class ItemGroupContentType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupContent';
    
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
                    'type' => fn() => $services->getType('ItemGroupContentTypes'),
                    'resolve' => fn() => [],
                    'description' => 'Content types from this group',
                ],
                'articles' => [
                    'type' => fn() => $services->getType('ArticleItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "article", 1 => $args]);
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
                'article' => [
                    'type' => fn() => $services->getType('ArticleItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "article"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the article',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the article',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the article',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the article',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the article',
                        ],
                    ],
                ],
                'folders' => [
                    'type' => fn() => $services->getType('FolderItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "folder", 1 => $args]);
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
                'folder' => [
                    'type' => fn() => $services->getType('FolderItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "folder"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the folder',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the folder',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the folder',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the folder',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the folder',
                        ],
                    ],
                ],
                'forms' => [
                    'type' => fn() => $services->getType('FormItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "form", 1 => $args]);
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
                'form' => [
                    'type' => fn() => $services->getType('FormItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "form"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the form',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the form',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the form',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the form',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the form',
                        ],
                    ],
                ],
                'tags' => [
                    'type' => fn() => $services->getType('TagItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "tag", 1 => $args]);
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
                'tag' => [
                    'type' => fn() => $services->getType('TagItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "tag"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the tag',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the tag',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the tag',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the tag',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the tag',
                        ],
                    ],
                ],
                'landingPages' => [
                    'type' => fn() => $services->getType('LandingPageItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "landing_page", 1 => $args]);
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
                'landingPage' => [
                    'type' => fn() => $services->getType('LandingPageItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "landing_page"]);
                    },
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the landing_page',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the landing_page',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the landing_page',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the landing_page',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the landing_page',
                        ],
                    ],
                ],
                'productCategoryTags' => [
                    'type' => fn() => $services->getType('ProductCategoryTagItemConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemsOfTypeAsConnection", ...[0 => "product_category_tag", 1 => $args]);
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
                'productCategoryTag' => [
                    'type' => fn() => $services->getType('ProductCategoryTagItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ItemOfType", ...[0 => $args, 1 => "product_category_tag"]);
                    },
                    'description' => 'No description available',
                    'args' => [
                        [
                            'name' => 'contentId',
                            'type' => Type::int(),
                            'description' => 'Content ID of the product_category_tag',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::string(),
                            'description' => 'Content remote ID of the product_category_tag',
                        ],
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'Location ID of the product_category_tag',
                        ],
                        [
                            'name' => 'locationRemoteId',
                            'type' => Type::string(),
                            'description' => 'Location remote ID of the product_category_tag',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'URL alias of the product_category_tag',
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