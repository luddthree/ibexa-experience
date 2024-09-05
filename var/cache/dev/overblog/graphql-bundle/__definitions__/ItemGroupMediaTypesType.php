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
final class ItemGroupMediaTypesType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ItemGroupMediaTypes';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'file' => [
                    'type' => fn() => $services->getType('FileItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "file"]]);
                    },
                ],
                'image' => [
                    'type' => fn() => $services->getType('ImageItemType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentType", ...[0 => ["identifier" => "image"]]);
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