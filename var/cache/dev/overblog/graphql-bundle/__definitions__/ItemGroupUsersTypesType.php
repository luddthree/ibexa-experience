<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ItemGroupUsersTypesType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupUsersTypes';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'editor' => [
                    'type' => fn() => $services->getType('EditorItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "editor"]]);
                    },
                ],
                'user' => [
                    'type' => fn() => $services->getType('UserItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "user"]]);
                    },
                ],
                'userGroup' => [
                    'type' => fn() => $services->getType('UserGroupItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "user_group"]]);
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