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
final class MatrixFieldDefinitionSettingsType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'MatrixFieldDefinitionSettings';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'columns' => [
                    'type' => fn() => Type::listOf($services->getType('MatrixFieldDefinitionColumn')),
                ],
                'minimumRows' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value["minimum_rows"];
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