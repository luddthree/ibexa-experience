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
final class UserGroupType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'UserGroup';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
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
                    'type' => fn() => $services->getType('UserGroupItem'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("Item", ["id" => $value->id]);
                    },
                    'description' => 'The User Group content item',
                ],
                'version' => [
                    'type' => fn() => $services->getType('Version'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->versionInfo;
                    },
                    'description' => 'Current version',
                ],
                'parentGroup' => [
                    'type' => fn() => $services->getType('UserGroup'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserGroupById", ...[0 => $value->parentId]);
                    },
                ],
                'subGroups' => [
                    'type' => fn() => Type::listOf($services->getType('UserGroup')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserGroupSubGroups", ...[0 => $value]);
                    },
                ],
                'users' => [
                    'type' => fn() => Type::listOf($services->getType('User')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UsersOfGroup", ...[0 => $value]);
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