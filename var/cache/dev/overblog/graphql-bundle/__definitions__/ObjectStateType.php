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
final class ObjectStateType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ObjectState';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'An Ibexa content object state.',
            'fields' => fn() => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The ObjectState\'s unique ID.',
                ],
                'identifier' => [
                    'type' => Type::string(),
                    'description' => 'The ObjectState\'s system identifier.',
                ],
                'priority' => [
                    'type' => Type::int(),
                    'description' => 'The ObjectState\'s priority used for ordering.',
                ],
                'languageCodes' => [
                    'type' => Type::listOf(Type::string()),
                    'description' => 'The ObjectStateGroup\'s language codes.',
                ],
                'group' => [
                    'type' => fn() => $services->getType('ObjectStateGroup'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getObjectStateGroup();
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