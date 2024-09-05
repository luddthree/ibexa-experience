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
final class DateAndTimeFieldValueType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'DateAndTimeFieldValue';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'text' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return (($value->value) ? ($value->value->getTimestamp()) : (null));
                    },
                    'description' => 'Unix timestamp',
                ],
                'formatted' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("DateTimeFormat", ...[0 => $args["format"], 1 => $value->value]);
                    },
                    'description' => 'Formatted date',
                    'args' => [
                        [
                            'name' => 'format',
                            'type' => Type::string(),
                            'description' => 'A format string compatible with PHP\'s date() function',
                        ],
                    ],
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