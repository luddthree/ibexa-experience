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
final class ImageFieldValueType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ImageFieldValue';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'text' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                    'description' => 'String representation of the value',
                ],
                'id' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->id;
                    },
                ],
                'alternativeText' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->alternativeText;
                    },
                ],
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
                'uri' => [
                    'type' => Type::string(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->uri;
                    },
                ],
                'width' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->width;
                    },
                ],
                'height' => [
                    'type' => Type::int(),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->height;
                    },
                ],
                'additionalData' => [
                    'type' => fn() => $services->getType('ImageAdditionalData'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value->additionalData;
                    },
                ],
                'variations' => [
                    'type' => fn() => Type::listOf($services->getType('ImageVariation')),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ImageVariations", ...[0 => $value->value, 1 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'identifier',
                            'type' => fn() => Type::nonNull(Type::listOf($services->getType('ImageVariationIdentifier'))),
                            'description' => 'One or more variation identifiers.',
                        ],
                    ],
                ],
                'variation' => [
                    'type' => fn() => $services->getType('ImageVariation'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ImageVariation", ...[0 => $value->value, 1 => $args]);
                    },
                    'args' => [
                        [
                            'name' => 'identifier',
                            'type' => fn() => Type::nonNull($services->getType('ImageVariationIdentifier')),
                            'description' => 'A variation identifier.',
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