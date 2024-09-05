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
final class BaseProductType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'BaseProduct';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'Ibexa Product',
            'fields' => fn() => [
                'code' => [
                    'type' => Type::string(),
                    'description' => 'Product\'s unique code',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'Product\'s name',
                ],
                'productType' => [
                    'type' => fn() => $services->getType('BaseProductType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ProductTypeByIdentifier", $value->getProductType()->getIdentifier());
                    },
                    'description' => 'Product Type which the product is based on',
                ],
                'thumbnail' => [
                    'type' => fn() => $services->getType('Thumbnail'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentThumbnail", $value->getContent());
                    },
                    'description' => 'Product\'s thumbnail',
                ],
                'createdAt' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'Product\'s creation date and time',
                ],
                'updatedAt' => [
                    'type' => fn() => $services->getType('DateTime'),
                    'description' => 'Product\'s last update date and time',
                ],
                'fields' => [
                    'type' => fn() => $services->getType('ProductContentFieldsInterface'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                    'description' => 'Underlying content\'s fields',
                ],
                'attributes' => [
                    'type' => fn() => $services->getType('ProductAttributesInterface'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $value;
                    },
                    'description' => 'Product\'s attributes',
                ],
                '_content' => [
                    'type' => fn() => $services->getType('Content'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentById", $value->getContent()->id);
                    },
                    'description' => 'Underlying content item',
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