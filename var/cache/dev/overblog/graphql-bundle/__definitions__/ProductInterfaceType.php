<?php

namespace Overblog\GraphQLBundle\__DEFINITIONS__;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use Overblog\GraphQLBundle\Definition\ConfigProcessor;
use Overblog\GraphQLBundle\Definition\GraphQLServices;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Type\GeneratedTypeInterface;

/**
 * THIS FILE WAS GENERATED AND SHOULD NOT BE EDITED MANUALLY.
 */
final class ProductInterfaceType extends InterfaceType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'ProductInterface';
    
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
                    'type' => fn() => $services->getType('ProductTypeInterface'),
                    'description' => 'Product Type which the product is based on',
                ],
                'thumbnail' => [
                    'type' => fn() => $services->getType('Thumbnail'),
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
                    'description' => 'Underlying content\'s fields',
                ],
                'attributes' => [
                    'type' => fn() => $services->getType('ProductAttributesInterface'),
                    'description' => 'Product\'s attributes',
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