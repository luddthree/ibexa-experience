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
final class AttributeDefinitionType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'AttributeDefinition';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Attribute name',
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'Attribute description',
                ],
                'identifier' => [
                    'type' => Type::string(),
                    'description' => 'Attribute identifier',
                ],
                'type' => [
                    'type' => fn() => $services->getType('AttributeType'),
                    'description' => 'Attribute type',
                ],
                'group' => [
                    'type' => fn() => $services->getType('AttributeGroup'),
                    'description' => 'Attribute group',
                ],
                'position' => [
                    'type' => Type::int(),
                    'description' => 'Attribute position',
                ],
                'options' => [
                    'type' => fn() => Type::listOf($services->getType('Option')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getOptions()->all();
                    },
                    'description' => 'Attribute options',
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