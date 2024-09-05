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
final class LocationType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Location';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa repository location.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The unique ID of the location.',
                ],
                'contentId' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The ID of the Content item referenced by the Location.',
                ],
                'priority' => [
                    'type' => Type::int(),
                    'description' => 'Position of the Location among its siblings when sorted using priority/',
                ],
                'hidden' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates that the Location is explicitly marked as hidden.',
                ],
                'invisible' => [
                    'type' => Type::boolean(),
                    'description' => 'Indicates that the Location is implicitly marked as hidden by a parent location',
                ],
                'remoteId' => [
                    'type' => Type::string(),
                    'description' => 'A global unique id of the content object',
                ],
                'parentLocationId' => [
                    'type' => Type::int(),
                    'description' => 'The id of the parent location',
                ],
                'parentLocation' => [
                    'type' => fn() => $services->getType('Location'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationById", ...[0 => $value->parentLocationId]);
                    },
                    'description' => 'The parent location',
                ],
                'pathString' => [
                    'type' => Type::string(),
                    'description' => 'The path to the Location in the Tree.',
                ],
                'path' => [
                    'type' => Type::listOf(Type::int()),
                    'description' => 'Same as $pathString but as array, e.g. [ 1, 2, 4, 23 ]',
                ],
                'depth' => [
                    'type' => Type::int(),
                    'description' => 'Depth location has in the location tree',
                ],
                'children' => [
                    'type' => fn() => $services->getType('LocationConnection'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationChildren", ...[0 => ["locationId" => $value->id], 1 => $args]);
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
                            'name' => 'sortBy',
                            'type' => fn() => Type::listOf($services->getType('LocationSortByOptions')),
                            'description' => 'A sort clause, or array of clauses. Add _desc after a clause to reverse it',
                        ],
                    ],
                ],
                'urlAliases' => [
                    'type' => fn() => Type::listOf($services->getType('LocationUrlAlias')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("LocationUrlAliases", ...[0 => $value, 1 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'custom',
                            'type' => Type::boolean(),
                        ],
                    ],
                ],
                'contentInfo' => [
                    'type' => fn() => $services->getType('Content'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getContentInfo();
                    },
                ],
                'content' => [
                    'type' => fn() => $services->getType('DomainContent'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("DomainContentItem", ...[0 => ["id" => $value->contentId], 1 => null]);
                    },
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