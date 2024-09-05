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
final class SelectionFieldDefinitionSettingsType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'SelectionFieldDefinitionSettings';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'isMultiple' => [
                    'type' => Type::boolean(),
                ],
                'options' => [
                    'type' => fn() => Type::listOf($services->getType('SelectionFieldDefinitionOption')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("SelectionFieldDefinitionOptions", ...[0 => $value["options"]]);
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