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
final class MapLocationFieldInputType extends InputObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'MapLocationFieldInput';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'address' => [
                    'type' => Type::string(),
                    'description' => 'Display address for the location.',
                ],
                'latitude' => [
                    'type' => Type::float(),
                    'description' => 'Latitude of the location',
                ],
                'longitude' => [
                    'type' => Type::float(),
                    'description' => 'Longitude of the location',
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