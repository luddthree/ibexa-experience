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
final class ProductCategoryTagItemEdgeType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ProductCategoryTagItemEdge';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An edge in a connection.',
            'fields' => fn() => [
                'node' => [
                    'type' => fn() => $services->getType('ProductCategoryTagItem'),
                    'description' => 'The item at the end of the edge.',
                ],
                'cursor' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'A cursor for use in pagination.',
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