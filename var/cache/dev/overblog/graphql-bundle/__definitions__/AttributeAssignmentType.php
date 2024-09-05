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
final class AttributeAssignmentType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'AttributeAssignment';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'isRequired' => [
                    'type' => Type::boolean(),
                    'description' => 'Determine if given attribute definition assignment is required',
                ],
                'isDiscriminator' => [
                    'type' => Type::boolean(),
                    'description' => 'Determine whether given attribute will be used for product variants',
                ],
                'attributeDefinition' => [
                    'type' => fn() => $services->getType('AttributeDefinition'),
                    'description' => 'Attribute assignment definition',
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