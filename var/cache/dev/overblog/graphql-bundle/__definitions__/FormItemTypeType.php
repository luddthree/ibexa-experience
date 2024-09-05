<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\ObjectType;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class FormItemTypeType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'FormItemType';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                '_info' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                ],
                'title' => [
                    'type' => fn() => $services->getType('TextLineFieldDefinition'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getFieldDefinition("title");
                    },
                ],
                'form' => [
                    'type' => fn() => $services->getType('MatrixFieldDefinition'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getFieldDefinition("form");
                    },
                ],
            ],
            'interfaces' => fn() => [
                $services->getType('ItemType'),
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