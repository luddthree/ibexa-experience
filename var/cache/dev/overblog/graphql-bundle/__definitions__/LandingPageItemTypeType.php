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
final class LandingPageItemTypeType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'LandingPageItemType';
    
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
                'name' => [
                    'type' => fn() => $services->getType('TextLineFieldDefinition'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getFieldDefinition("name");
                    },
                    'description' => 'Title',
                ],
                'description' => [
                    'type' => fn() => $services->getType('TextLineFieldDefinition'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getFieldDefinition("description");
                    },
                    'description' => 'Landing page description',
                ],
                'page' => [
                    'type' => fn() => $services->getType('MatrixFieldDefinition'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->getFieldDefinition("page");
                    },
                    'description' => 'Landing page',
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