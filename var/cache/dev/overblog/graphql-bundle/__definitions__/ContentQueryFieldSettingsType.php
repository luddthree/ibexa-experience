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
final class ContentQueryFieldSettingsType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ContentQueryFieldSettings';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'queryType' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value["QueryType"];
                    },
                    'description' => 'Identifier of the query type executed by the field',
                ],
                'parameters' => [
                    'type' => fn() => Type::listOf($services->getType('ContentQueryFieldParameter')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("QueryFieldDefinitionParameters", ...[0 => $value["Parameters"]]);
                    },
                    'description' => 'Parameters used to generate the Query from the Query type',
                ],
                'returnedType' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value["ReturnedType"];
                    },
                    'description' => 'Content type returned by the field',
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