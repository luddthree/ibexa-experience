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
final class MediaFieldValueType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'MediaFieldValue';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'fileName' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->fileName;
                    },
                ],
                'fileSize' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->fileSize;
                    },
                ],
                'mimeType' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->mimeType;
                    },
                ],
                'uri' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->uri;
                    },
                ],
                'text' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->uri;
                    },
                    'description' => 'String representation of the value',
                ],
                'hasController' => [
                    'type' => Type::boolean(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->hasController;
                    },
                    'description' => 'If the media has a controller when being displayed.',
                ],
                'autoplay' => [
                    'type' => Type::boolean(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->autoplay;
                    },
                    'description' => 'If the media should be automatically played.',
                ],
                'loop' => [
                    'type' => Type::boolean(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->loop;
                    },
                    'description' => 'If the media should be played in a loop.',
                ],
                'height' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->height;
                    },
                    'description' => 'Height of the media.',
                ],
                'width' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->width;
                    },
                    'description' => 'Width of the media.',
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