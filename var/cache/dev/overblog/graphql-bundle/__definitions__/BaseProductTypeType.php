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
final class BaseProductTypeType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'BaseProductType';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'description' => 'Ibexa Product Type',
            'fields' => fn() => [
                'identifier' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'Product Type\'s unique identifier',
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'Product Type\'s name',
                ],
                'attributesDefinitions' => [
                    'type' => fn() => Type::listOf($services->getType('AttributeAssignment')),
                    'description' => 'Product Type\'s assigned attribute definitions',
                ],
                '_contentType' => [
                    'type' => fn() => $services->getType('ContentType'),
                    'resolve' => function ($value, $args, $context, $info) use ($services) {
                        return $services->query("ContentTypeById", $value->getContentType()->id);
                    },
                    'description' => 'Underlying content type',
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