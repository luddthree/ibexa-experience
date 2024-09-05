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
final class CustomerPortalItemConnectionType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'CustomerPortalItemConnection';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'A connection to a list of items.',
            'fields' => fn() => [
                'sliceSize' => [
                    'type' => Type::nonNull(Type::int()),
                ],
                'orderBy' => [
                    'type' => Type::string(),
                ],
                'pageInfo' => [
                    'type' => fn() => Type::nonNull($services->getType('PageInfo')),
                    'description' => 'Information to aid in pagination.',
                ],
                'edges' => [
                    'type' => fn() => Type::listOf($services->getType('CustomerPortalItemEdge')),
                    'description' => 'Information to aid in pagination.',
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