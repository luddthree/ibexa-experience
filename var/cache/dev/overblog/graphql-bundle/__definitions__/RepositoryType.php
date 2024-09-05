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
final class RepositoryType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Repository';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'Ibexa repository',
            'fields' => fn() => [
                'location' => [
                    'type' => fn() => $services->getType('Location'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Location", ...[0 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'locationId',
                            'type' => Type::int(),
                            'description' => 'A location id',
                        ],
                        [
                            'name' => 'remoteId',
                            'type' => Type::int(),
                            'description' => 'A location remote id',
                        ],
                        [
                            'name' => 'urlAlias',
                            'type' => Type::string(),
                            'description' => 'A location url alias: \'path/to/content-item\'',
                        ],
                    ],
                ],
                'contentType' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'id',
                            'type' => Type::int(),
                            'description' => 'Resolves using the unique ContentType id.',
                        ],
                        [
                            'name' => 'identifier',
                            'type' => Type::string(),
                            'description' => 'Resolves using the unique ContentType identifier.',
                        ],
                    ],
                ],
                'contentTypes' => [
                    'type' => fn() => Type::listOf($services->getType('ContentType')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentTypesFromGroup", ...[0 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'groupId',
                            'type' => Type::string(),
                        ],
                        [
                            'name' => 'groupIdentifier',
                            'type' => Type::string(),
                        ],
                    ],
                ],
                'objectStateGroup' => [
                    'type' => fn() => $services->getType('ObjectStateGroup'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ObjectStateGroupById", ...[0 => $args]);
                    },
                    'description' => 'Fetches Object State Group by ID.',
                    'args' => [
                        [
                            'name' => 'id',
                            'type' => Type::int(),
                            'description' => 'ID of the Object State Group',
                        ],
                    ],
                ],
                'objectStateGroups' => [
                    'type' => fn() => Type::listOf($services->getType('ObjectStateGroup')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ObjectStateGroups", ...[]);
                    },
                    'description' => 'Fetches all Object State Groups.',
                ],
                'objectState' => [
                    'type' => fn() => $services->getType('ObjectState'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ObjectStateById", ...[0 => $args]);
                    },
                    'description' => 'Fetches Object State by ID.',
                    'args' => [
                        [
                            'name' => 'id',
                            'type' => Type::int(),
                            'description' => 'ID of the Object State',
                        ],
                    ],
                ],
                'objectStates' => [
                    'type' => fn() => Type::listOf($services->getType('ObjectState')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ObjectStatesByGroupId", ...[0 => $args]);
                    },
                    'description' => 'Fetches Object States assigned to given Group ID.',
                    'args' => [
                        [
                            'name' => 'groupId',
                            'type' => Type::int(),
                            'description' => 'ID of the ObjectStateGroup',
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