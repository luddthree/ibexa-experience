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
final class PlatformType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'Platform';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_repository' => [
                    'type' => fn() => $services->getType('Repository'),
                    'resolve' => fn() => [],
                    'description' => 'Ibexa repository API',
                ],
                'node' => [
                    'type' => fn() => $services->getType('Node'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("relay_node_field", $args, $context, $info, function ($value) use ($services, $args, $context, $info) {
                            return $services->query("node", ...[0 => $value]);
                        });
                    },
                    'description' => 'Fetches an object given its ID',
                    'args' => [
                        [
                            'name' => 'id',
                            'type' => Type::nonNull(Type::id()),
                            'description' => 'The ID of an object',
                        ],
                    ],
                ],
                'item' => [
                    'type' => fn() => $services->getType('Item'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Item", ...[0 => $args]);
                    },
                    'description' => 'An item, whatever its type',
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