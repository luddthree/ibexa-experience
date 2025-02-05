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
final class LocationConnectionType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'LocationConnection';
    
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
                'totalCount' => [
                    'type' => Type::int(),
                ],
                'pages' => [
                    'type' => fn() => Type::listOf($services->getType('ConnectionPage')),
                ],
                'pageInfo' => [
                    'type' => fn() => Type::nonNull($services->getType('PageInfo')),
                    'description' => 'Information to aid in pagination.',
                ],
                'edges' => [
                    'type' => fn() => Type::listOf($services->getType('LocationEdge')),
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