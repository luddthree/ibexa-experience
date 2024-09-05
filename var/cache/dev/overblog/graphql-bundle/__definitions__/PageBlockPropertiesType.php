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
final class PageBlockPropertiesType extends ObjectType implements GeneratedTypeInterface, AliasedInterface
{
    public const NAME = 'PageBlockProperties';
    
    public function __construct(ConfigProcessor $configProcessor, GraphQLServices $services)
    {
        $config = [
            'name' => self::NAME,
            'fields' => fn() => [
                'id' => [
                    'type' => Type::string(),
                ],
                'type' => [
                    'type' => Type::string(),
                ],
                'name' => [
                    'type' => Type::string(),
                ],
                'view' => [
                    'type' => Type::string(),
                ],
                'class' => [
                    'type' => Type::string(),
                ],
                'style' => [
                    'type' => Type::string(),
                ],
                'compiled' => [
                    'type' => Type::string(),
                ],
                'since' => [
                    'type' => fn() => $services->getType('DateTime'),
                ],
                'till' => [
                    'type' => fn() => $services->getType('DateTime'),
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