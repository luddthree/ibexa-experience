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
final class UserType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'User';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa repository User.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'The Content item\'s id. Shortcut to ContentInfo {id}.',
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->contentInfo->name;
                    },
                ],
                'content' => [
                    'type' => fn() => $services->getType('UserItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Item", ["id" => $value->id]);
                    },
                ],
                'version' => [
                    'type' => fn() => $services->getType('Version'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->versionInfo;
                    },
                    'description' => 'Current version metadata',
                ],
                'enabled' => [
                    'type' => Type::boolean(),
                ],
                'maxLogin' => [
                    'type' => Type::int(),
                ],
                'groups' => [
                    'type' => fn() => Type::listOf($services->getType('UserGroup')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserGroupsByUserId", ...[0 => $value->id]);
                    },
                ],
                'thumbnail' => [
                    'type' => fn() => $services->getType('Thumbnail'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Thumbnail", $value->getThumbnail());
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