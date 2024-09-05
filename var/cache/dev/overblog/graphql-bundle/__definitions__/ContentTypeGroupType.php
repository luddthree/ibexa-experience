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
final class ContentTypeGroupType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentTypeGroup';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'id' => [
                    'type' => Type::int(),
                ],
                'identifier' => [
                    'type' => Type::string(),
                ],
                'creationDate' => [
                    'type' => fn() => $services->getType('DateTime'),
                ],
                'modificationDate' => [
                    'type' => fn() => $services->getType('DateTime'),
                ],
                'creatorId' => [
                    'type' => Type::int(),
                ],
                'creator' => [
                    'type' => fn() => $services->getType('User'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserById", ...[0 => $value->creatorId]);
                    },
                ],
                'modifierId' => [
                    'type' => Type::int(),
                ],
                'modifier' => [
                    'type' => fn() => $services->getType('User'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("UserById", ...[0 => $value->modifierId]);
                    },
                ],
                'contentTypes' => [
                    'type' => fn() => Type::listOf($services->getType('ContentType')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentTypesFromGroup", ["groupId" => $value->id]);
                    },
                ],
                'groups' => [
                    'type' => fn() => Type::listOf($services->getType('ContentTypeGroup')),
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