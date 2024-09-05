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
final class AttributeGroupType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'AttributeGroup';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'identifier' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Attribute group identifier',
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Attribute group name',
                ],
                'position' => [
                    'type' => Type::int(),
                    'description' => 'Attribute group position',
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