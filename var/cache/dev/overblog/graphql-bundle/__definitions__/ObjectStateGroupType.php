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
final class ObjectStateGroupType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ObjectStateGroup';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa content object state group.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The ObjectStateGroup\'s unique ID.',
                ],
                'identifier' => [
                    'type' => Type::string(),
                    'description' => 'The ObjectStateGroup\'s system identifier.',
                ],
                'defaultLanguageCode' => [
                    'type' => Type::string(),
                    'description' => 'The ObjectStateGroup\'s default language code.',
                ],
                'languageCodes' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'The ObjectStateGroup\'s language codes.',
                ],
                'states' => [
                    'type' => fn() => Type::listOf($services->getType('ObjectState')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ObjectStatesByGroup", ...[0 => $value]);
                    },
                    'description' => 'List of ObjectStates under ObjectStateGroup.',
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