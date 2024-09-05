<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class FieldCriterionInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'FieldCriterionInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'target' => [
                    'type' => Type::string(),
                    'description' => 'A field definition identifier',
                ],
                'between' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'Between two values',
                ],
                'contains' => [
                    'type' => Type::string(),
                    'description' => 'Contains the value',
                ],
                'in' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'Equal to one of the given values',
                ],
                'eq' => [
                    'type' => Type::string(),
                    'description' => 'Equal to the value',
                ],
                'gt' => [
                    'type' => Type::string(),
                    'description' => 'Greater than the value',
                ],
                'gte' => [
                    'type' => Type::string(),
                    'description' => 'Greater than or equal to the value',
                ],
                'lt' => [
                    'type' => Type::string(),
                    'description' => 'Lesser than the value',
                ],
                'lte' => [
                    'type' => Type::string(),
                    'description' => 'Lesser than or equal to the value',
                ],
                'like' => [
                    'type' => Type::string(),
                    'description' => 'Like the value',
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