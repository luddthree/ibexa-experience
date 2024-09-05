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
final class DateTimeType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'DateTime';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'A date',
            'fields' => fn() => [
                'format' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("DateTimeFormat", ...[0 => $value, 1 => $args]);
                    },
                    'description' => 'Date formatted with a date() format',
                    'args' => [
                        [
                            'name' => 'pattern',
                            'type' => Type::string(),
                            'description' => 'A pattern compatible with date()',
                        ],
                        [
                            'name' => 'constant',
                            'type' => fn() => $services->getType('DateFormatConstant'),
                        ],
                    ],
                ],
                'timestamp' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getTimestamp();
                    },
                    'description' => 'The raw string value',
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